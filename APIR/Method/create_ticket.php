<?php
use Config\Conexion as Conexion;
if (session_status() === PHP_SESSION_NONE) session_start();

// Ensure constants and autoloader
if (!defined('ROOT')) {
    require_once __DIR__ . '/../../Define.php';
}
if (!class_exists('\\Config\\AutoLoad')) {
    require_once __DIR__ . '/../../Config/AutoLoad.php';
    \Config\AutoLoad::run();
}

$Conexion = new Conexion();
$Conexion = $Conexion->getConexion();

// Accept POST form-data or JSON
$input = $_POST;
$raw = @file_get_contents('php://input');
if ($raw && empty($input)) {
    $json = json_decode($raw, true);
    if (is_array($json)) $input = $json;
}

$service_id = isset($input['service_id']) ? $input['service_id'] : (isset($_POST['service_id'])?$_POST['service_id']: null);
$client_type_id = isset($input['client_type_id']) ? $input['client_type_id'] : (isset($_POST['client_type_id'])?$_POST['client_type_id']: null);

if (empty($service_id) || empty($client_type_id)) {
    echo json_encode(['success'=>false,'message'=>'service_id o client_type_id faltante']);
    http_response_code(400);
    exit;
}

// Load models
$ticketModel = new \Models\Ticket();

// helper: fetch names
function fetchName($pdo, $table, $id) {
    try {
        $stmt = $pdo->prepare("SELECT `name` FROM {$table} WHERE id = :id LIMIT 1");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $r = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $r && isset($r['name']) ? $r['name'] : null;
    } catch (\Exception $e) {
        return null;
    }
}

$serviceName = fetchName($Conexion, 'Services', $service_id);
$clientTypeName = fetchName($Conexion, 'ClientTypes', $client_type_id);

$prefix = 'C'; // default
$serviceLower = strtolower($serviceName ?? '');
$clientLower = strtolower($clientTypeName ?? '');

// Rules:
// - Cliente general + Caja -> C
// - Cliente general + Atención -> S
// - Tercera edad + Caja -> MV
// - Tercera edad + Atención -> S
if (strpos($clientLower, 'tercera') !== false || strpos($clientLower, 'edad') !== false) {
    // tercera edad
    if (strpos($serviceLower, 'caja') !== false) {
        $prefix = 'MV';
    } else {
        $prefix = 'S';
    }
} else {
    // general
    if (strpos($serviceLower, 'caja') !== false) {
        $prefix = 'C';
    } else {
        $prefix = 'S';
    }
}

// generate next id using model
$nextId = $ticketModel->getNewId();
$nextIdInt = intval($nextId);
$codeNumber = str_pad($nextIdInt, 3, '0', STR_PAD_LEFT);
$ticket_code = strtoupper($prefix) . '-' . $codeNumber;

// Build entity
$entity = new \stdClass();
$entity->id = $nextIdInt; // SP_Ticket expects id
$entity->ticket_code = $ticket_code;
$entity->service_id = (int)$service_id;
$entity->client_type_id = (int)$client_type_id;
// Determine status id for 'En espera' dynamically (fallback to 1)
$status_id = 1;
try {
    $sstmt = $Conexion->prepare("SELECT id FROM TicketStatuses WHERE LOWER(name) LIKE :pattern LIMIT 1");
    $pattern = '%espera%';
    $sstmt->bindParam(':pattern', $pattern);
    $sstmt->execute();
    $sr = $sstmt->fetch(\PDO::FETCH_ASSOC);
    if ($sr && isset($sr['id'])) {
        $status_id = (int)$sr['id'];
    }
} catch (\Exception $e) {
    // keep fallback 1
}
$entity->status_id = $status_id; // default status (En espera if present)
// kiosk user: use 'General' user if exists (username 'general')
try {
    $u = $Conexion->prepare("SELECT id FROM Users WHERE username = 'general' LIMIT 1");
    $u->execute();
    $ur = $u->fetch(\PDO::FETCH_ASSOC);
    $entity->user_id = $ur && isset($ur['id']) ? $ur['id'] : 1;
} catch (\Exception $e) {
    $entity->user_id = 1;
}
$entity->date_time = date('Y-m-d H:i:s');

// Save ticket
try {
    $ticketModel->save($entity);
} catch (\Exception $e) {
    echo json_encode(['success'=>false,'message'=>'Error guardando ticket: '.$e->getMessage()]);
    http_response_code(500);
    exit;
}

// Audit
try {
    $audit = new \Models\Audit();
    $audit->log(null, null, $entity->id, 'CREADO', 'Ticket creado desde: ' . $entity->ticket_code);
} catch (\Exception $e) {
    // ignore
}

// Return response including pdf url
$pdfUrl = '/APIR/index.php?method=ticket_pdf&id=' . urlencode($entity->id);

echo json_encode(['success'=>true, 'id'=>$entity->id, 'ticket_code'=>$entity->ticket_code, 'pdf_url'=>$pdfUrl]);
http_response_code(200);
