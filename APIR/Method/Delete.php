<?php
    use Config\Conexion as Conexion;
    if (session_status() === PHP_SESSION_NONE) session_start();

    // Ensure project constants and autoloader are available when methods are called directly
    if (!defined('ROOT')) {
        require_once __DIR__ . '/../../Define.php';
    }
    if (!class_exists('\Config\AutoLoad')) {
        require_once __DIR__ . '/../../Config/AutoLoad.php';
        \Config\AutoLoad::run();
    }
    $Conexion = new Conexion();
    $Conexion = $Conexion->getConexion();

    $id = isset($_POST['id']) ? $_POST['id'] : '';
    $table = isset($_POST['table']) ? $_POST['table'] : '';

    if(empty($id)) {
        $json = array(
            'message' => 'Id no proporcionado',
            'success' => false
        );  

        echo json_encode($json);
        http_response_code(200);
        exit;
    }

    if(empty($table)){
        $json = array (
            'message' => 'Tabla no proporcionada',
            'success' => false
        );

        echo json_encode($json);
        http_response_code(200);
        exit;
    }

    $logFile = __DIR__ . '/../logs/delete_errors.log';
    // Normalizar algunos alias comunes de tablas
    $aliases = [
        'user' => 'Users',
        'users' => 'Users',
        'usuario' => 'Users',
        'usuarios' => 'Users',
    ];
    $tblLower = strtolower($table);
    if (isset($aliases[$tblLower])) {
        $table = $aliases[$tblLower];
        $tblLower = strtolower($table);
    }

    $ok = false;
    $msg = '';

    if ($tblLower === 'tickets' || $tblLower === 'ticket') {
        try {
            $Conexion->beginTransaction();
            $delAudit = $Conexion->prepare('DELETE FROM audits WHERE ticket_id = :id');
            $delAudit->bindParam(':id', $id);
            $delAudit->execute();
            $audDeleted = $delAudit->rowCount();

            $delTicket = $Conexion->prepare('DELETE FROM tickets WHERE id = :id');
            $delTicket->bindParam(':id', $id);
            $delTicket->execute();
            $ticketDeleted = $delTicket->rowCount();

            $Conexion->commit();
            $ok = true;
            @file_put_contents($logFile, '['.date('Y-m-d H:i:s').'] deleted ticket id='.print_r($id,true)." audits_deleted={$audDeleted} tickets_deleted={$ticketDeleted}\n", FILE_APPEND);
        } catch (\PDOException $ex) {
            try { $Conexion->rollBack(); } catch (\Exception $__) {}
            $msg = $ex->getMessage();
            @file_put_contents($logFile, '['.date('Y-m-d H:i:s').'] transactional delete error: '.$ex->getMessage()."\nTrace:".$ex->getTraceAsString()."\n", FILE_APPEND);
            $ok = false;
        }
    } else {
        // Try stored procedure first, fallback to direct DELETE
        try {
            $sql = "call SP_Delete{$table} (:id);";
            $stmt = $Conexion->prepare($sql);
            $stmt->bindParam(':id', $id);
            $ok = $stmt->execute();
        } catch (\PDOException $ex) {
            $msg = $ex->getMessage();
            @file_put_contents($logFile, '['.date('Y-m-d H:i:s').'] SP_Delete error for table '.print_r($table,true).' id='.print_r($id,true).': '.$ex->getMessage()."\nTrace:".$ex->getTraceAsString()."\n", FILE_APPEND);
            try {
                $safeTable = preg_replace('/[^A-Za-z0-9_]/','',$table);
                $delStmt = $Conexion->prepare("DELETE FROM {$safeTable} WHERE id = :id");
                $delStmt->bindParam(':id', $id);
                $ok = $delStmt->execute();
            } catch (\Exception $inner) {
                $ok = false;
                $msg = $inner->getMessage();
                @file_put_contents($logFile, '['.date('Y-m-d H:i:s').'] direct DELETE error for table '.print_r($safeTable,true).' id='.print_r($id,true).': '.$inner->getMessage()."\nTrace:".$inner->getTraceAsString()."\n", FILE_APPEND);
            }
        }
    }

    if ($ok) {
        $json = array(
            'message' => "Registro eliminado correctamente!",
            'success' => true
        );
        echo json_encode($json);
        http_response_code(200);
    } else {
        $json = array(
            'message' => "Error al eliminar el registro de la tabla $table!: " . ($msg ? $msg : ''),
            'success' => false
        );

        echo json_encode($json);
        http_response_code(200);
    }

?>