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

require_once __DIR__ . '/../../Vendor/fpdf/fpdf.php';

$Conexion = new Conexion();
$Conexion = $Conexion->getConexion();

$id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['id'])?intval($_POST['id']):0);
if (!$id) {
    echo 'Id no proporcionado';
    http_response_code(400);
    exit;
}

// Fetch ticket
try {
    $stmt = $Conexion->prepare('SELECT t.id, t.ticket_code, s.name AS service_name, ct.name AS client_type_name, t.date_time FROM Tickets t LEFT JOIN Services s ON s.id = t.service_id LEFT JOIN ClientTypes ct ON ct.id = t.client_type_id WHERE t.id = :id LIMIT 1');
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $ticket = $stmt->fetch(\PDO::FETCH_ASSOC);
    if (!$ticket) {
        echo 'Ticket no encontrado';
        http_response_code(404);
        exit;
    }
} catch (\Exception $e) {
    echo 'Error: ' . $e->getMessage();
    http_response_code(500);
    exit;
}

// Create PDF (A6 size explicit 105x148 mm)
$pdf = new FPDF('P','mm', array(105,148));
$pdf->AddPage();
$pdf->SetFont('Helvetica','B',20);
$pdf->SetY(20);
$pdf->Cell(0,10, 'TICKET', 0, 1, 'C');

$pdf->SetFont('Helvetica','B',36);
$pdf->Ln(6);
$pdf->Cell(0,20, $ticket['ticket_code'], 0, 1, 'C');

$pdf->SetFont('Helvetica','',12);
$pdf->Ln(2);
$pdf->Cell(0,8, 'Servicio: ' . ($ticket['service_name'] ?? ''), 0, 1, 'C');
$pdf->Cell(0,8, 'Tipo cliente: ' . ($ticket['client_type_name'] ?? ''), 0, 1, 'C');
$pdf->Cell(0,8, 'Fecha: ' . ($ticket['date_time'] ?? ''), 0, 1, 'C');

// Output as download
$pdf->Output('I', 'ticket_'.$ticket['ticket_code'].'.pdf');
exit;
