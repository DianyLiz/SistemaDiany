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

$id = isset($input['id']) ? $input['id'] : null;
$action = isset($input['action']) ? strtolower($input['action']) : null;
$service_id = isset($input['service_id']) ? $input['service_id'] : null;

if (empty($id) || empty($action)) {
    echo json_encode(['success'=>false,'message'=>'id y action son requeridos']);
    http_response_code(400);
    exit;
}

// helper: find status id by name patterns
function findStatusId($pdo, $patterns) {
    foreach ($patterns as $p) {
        $stmt = $pdo->prepare("SELECT id FROM TicketStatuses WHERE LOWER(name) LIKE :pattern LIMIT 1");
        $pat = '%' . strtolower($p) . '%';
        $stmt->bindParam(':pattern', $pat);
        $stmt->execute();
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($r && isset($r['id'])) return (int)$r['id'];
    }
    return null;
}

try {
    $ticketModel = new \Models\Ticket();
    $t = $ticketModel->getForId($id);
    if (!$t) {
        echo json_encode(['success'=>false,'message'=>'Ticket no encontrado']);
        http_response_code(404);
        exit;
    }

    $newStatusId = null;
    $detail = '';

    if ($action === 'take') {
        // mark as being attended -> prefer 'abierto' or 'atenc'
        $newStatusId = findStatusId($Conexion, ['abierto','atenc','atención','en atención']);
        $detail = 'Ticket tomado para atención';
    } elseif ($action === 'close' || $action === 'cerrar') {
        $newStatusId = findStatusId($Conexion, ['cerrado','cerrar']);
        $detail = 'Ticket cerrado';
    } elseif ($action === 'change_service' || $action === 'cambiar' || $action === 'cambiar_servicio') {
        // change service and set to 'En espera'
        if (empty($service_id)) {
            echo json_encode(['success'=>false,'message'=>'service_id requerido para cambiar servicio']);
            http_response_code(400);
            exit;
        }
        // If the ticket is currently taken by the same user performing the change,
        // keep it as 'abierto' (in attention) and just change service. Otherwise
        // move it back to 'en espera' so it returns to queue for others.
        $currentUserId = isset($_SESSION['system']['user_id']) ? $_SESSION['system']['user_id'] : null;
        if ($currentUserId && isset($t->user_id) && $t->user_id == $currentUserId) {
            $newStatusId = findStatusId($Conexion, ['abierto','atenc','atención','en atención']);
            $detail = 'Servicio cambiado (mismo operador) a id ' . $service_id;
        } else {
            $newStatusId = findStatusId($Conexion, ['espera','en espera']);
            $detail = 'Servicio cambiado a id ' . $service_id;
        }
    } else {
        echo json_encode(['success'=>false,'message'=>'acción no soportada']);
        http_response_code(400);
        exit;
    }

    // if we couldn't find a status by name, fallback to existing or 1
    if (empty($newStatusId)) {
        $newStatusId = isset($t->status_id) ? $t->status_id : 1;
    }

    // perform update
    // When taking a ticket, update date_time to NOW() so we can track when it was taken
    $sql = "UPDATE Tickets SET status_id = :status_id";
    $params = [':status_id' => $newStatusId, ':id' => $id];
    if ($action === 'change_service' || strpos($action, 'cambiar') !== false) {
        $sql .= ", service_id = :service_id";
        $params[':service_id'] = $service_id;
    }
    // Optionally set the user taking the ticket to current session user
    $currentUserId = isset($_SESSION['system']['user_id']) ? $_SESSION['system']['user_id'] : null;
    if ($action === 'take') {
        // set taken timestamp
        $sql .= ", date_time = NOW()";
        if ($currentUserId) {
            $sql .= ", user_id = :user_id";
            $params[':user_id'] = $currentUserId;
        }
    } else {
        if ($action === 'close') {
            $sql .= ", user_id = NULL";
        }
    }
    $sql .= " WHERE id = :id";

    $stmt = $Conexion->prepare($sql);
    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v);
    }
    $stmt->execute();

    // If change_service and the current user is the operator for this ticket, persist user_id and update date_time
    if (($action === 'change_service' || strpos($action, 'cambiar') !== false) && $currentUserId && isset($t->user_id) && $t->user_id == $currentUserId) {
        try {
            $uSql = "UPDATE Tickets SET user_id = :user_id, date_time = NOW() WHERE id = :id";
            $ustmt = $Conexion->prepare($uSql);
            $ustmt->bindValue(':user_id', $currentUserId);
            $ustmt->bindValue(':id', $id);
            $ustmt->execute();
        } catch (\Exception $e) {
            // ignore secondary failure
        }
    }

    // audit log
    try {
        $audit = new \Models\Audit();
        $auditUser = $currentUserId ?: (isset($_SESSION['system']['user_id'])?$_SESSION['system']['user_id']:null);
        $audit->log($auditUser, null, $id, strtoupper($action), $detail);
    } catch (\Exception $e) {
        // ignore
    }

    // return updated ticket info to help frontends refresh from server
    try {
        $updated = $ticketModel->getForId($id);
        $updatedArr = $updated ? (array)$updated : null;
        // resolve desk id for the ticket's user
        if ($updatedArr && isset($updatedArr['user_id'])) {
            try {
                $dstmt = $Conexion->prepare("SELECT id FROM ServiceDesks WHERE user_id = :uid LIMIT 1");
                $dstmt->bindValue(':uid', $updatedArr['user_id']);
                $dstmt->execute();
                $drow = $dstmt->fetch(PDO::FETCH_ASSOC);
                $updatedArr['serviceDeskId'] = $drow && isset($drow['id']) ? $drow['id'] : $updatedArr['user_id'];
            } catch (\Exception $e) {
                $updatedArr['serviceDeskId'] = $updatedArr['user_id'];
            }
        }
    } catch (\Exception $e) {
        $updatedArr = null;
    }

    echo json_encode(['success'=>true,'message'=>'Ticket actualizado','ticket'=>$updatedArr]);
    http_response_code(200);
    exit;

} catch (\Exception $e) {
    echo json_encode(['success'=>false,'message'=>'Error: '.$e->getMessage()]);
    http_response_code(500);
    exit;
}

?>
