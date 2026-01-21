<?php
use Config\Conexion as Conexion;
if (session_status() === PHP_SESSION_NONE) session_start();

// Allow public access with API credentials if no session
// Ensure autoload/constants
if (!defined('ROOT')) require_once __DIR__ . '/../../Define.php';
if (!class_exists('\\Config\\AutoLoad')) { require_once __DIR__ . '/../../Config/AutoLoad.php'; \Config\AutoLoad::run(); }

$Conexion = new Conexion();
$Conexion = $Conexion->getConexion();

// simple auth for public endpoints
if(!isset($_SESSION["system"]["username"])) {
    $post = $_POST;
    $raw = @file_get_contents('php://input');
    if ($raw && empty($post)) {
        $json = json_decode($raw, true);
        if (is_array($json)) $post = $json;
    }
    $uid = isset($post['uid']) ? $post['uid'] : '';
    $pw = isset($post['pw']) ? $post['pw'] : '';
    if ($uid !== 'consultasAPI' || $pw !== 'API*Data123*') {
        echo json_encode(['success'=>false,'message'=>'No Autenticado']);
        http_response_code(401);
        exit;
    }
}

try {
    // Select the latest 'abierto' as current (most recently taken), fallback to earliest 'espera'
    $stmt = $Conexion->prepare("SELECT t.id, t.ticket_code, s.name AS service_name, ts.name AS status_name, t.date_time
                                FROM Tickets t
                                LEFT JOIN Services s ON s.id = t.service_id
                                LEFT JOIN TicketStatuses ts ON ts.id = t.status_id
                                WHERE LOWER(COALESCE(ts.name,'')) LIKE '%abiert%'
                                ORDER BY t.date_time DESC LIMIT 1");
    $stmt->execute();
    $currentRow = $stmt->fetch(PDO::FETCH_ASSOC);

    // Queue: tickets in 'en espera' ordered FIFO by date_time ASC
    $qstmt = $Conexion->prepare("SELECT t.id, t.ticket_code, s.name AS service_name, ts.name AS status_name, t.date_time
                                FROM Tickets t
                                LEFT JOIN Services s ON s.id = t.service_id
                                LEFT JOIN TicketStatuses ts ON ts.id = t.status_id
                                WHERE LOWER(COALESCE(ts.name,'')) LIKE '%espera%'
                                ORDER BY t.date_time ASC");
    $qstmt->execute();
    $rows = $qstmt->fetchAll(PDO::FETCH_ASSOC);

    $current = null;
    if ($currentRow && isset($currentRow['id'])) {
        $current = [ 'id' => $currentRow['id'], 'code' => $currentRow['ticket_code'], 'service' => $currentRow['service_name'], 'status' => $currentRow['status_name'], 'date_time' => $currentRow['date_time'] ];
    } else {
        // fallback: take first espera as current (if any)
        if (!empty($rows)) {
            $first = array_shift($rows);
            $current = [ 'id' => $first['id'], 'code' => $first['ticket_code'], 'service' => $first['service_name'], 'status' => $first['status_name'], 'date_time' => $first['date_time'] ];
        }
    }

    $queue = [];
    foreach ($rows as $r) {
        $queue[] = [ 'id' => $r['id'], 'code' => $r['ticket_code'], 'service' => $r['service_name'], 'status' => $r['status_name'], 'date_time' => $r['date_time'] ];
    }

    // Also return all 'taken' / 'abierto' tickets so public displays can show per-caja states
    $taken = [];
    try {
        $tstmt = $Conexion->prepare("SELECT t.id, t.ticket_code, t.service_id, s.name AS service_name, t.user_id, ts.name AS status_name, t.date_time, sd.id AS desk_id, sd.desk_name AS desk_name
                                FROM Tickets t
                                LEFT JOIN Services s ON s.id = t.service_id
                                LEFT JOIN TicketStatuses ts ON ts.id = t.status_id
                    LEFT JOIN ServiceDesks sd ON sd.user_id = t.user_id
                                WHERE LOWER(COALESCE(ts.name,'')) LIKE '%abiert%'
                                ORDER BY t.date_time DESC");
        $tstmt->execute();
        $trows = $tstmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($trows as $tr) {
            $deskId = isset($tr['desk_id']) && $tr['desk_id'] ? $tr['desk_id'] : $tr['user_id'];
            $deskName = isset($tr['desk_name']) ? $tr['desk_name'] : null;
            $taken[] = [ 'id' => $tr['id'], 'code' => $tr['ticket_code'], 'service' => $tr['service_name'], 'status' => $tr['status_name'], 'date_time' => $tr['date_time'], 'serviceDeskId' => $deskId, 'serviceDeskName' => $deskName ];
        }
    } catch (\Exception $e) {
        // ignore, taken remains empty
    }

    echo json_encode(['success'=>true,'current'=>$current,'queue'=>$queue,'taken'=>$taken]);
    http_response_code(200);
    exit;

} catch (\Exception $e) {
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
    http_response_code(500);
    exit;
}

?>
