<?php
namespace Controllers;

use Models\User as User;

class ReportController {
    // /Report/UserReport
    public function UserReport() {
        // Evitar que la plantilla se cargue (además de la detección en index.php)
        global $is_pdf_report; $is_pdf_report = true;

        // Cargar clases necesarias
        if (!class_exists('CustomPDF')) {
            require_once ROOT . 'Lib' . DS . 'CustomPDF.php';
        }

        // Obtener datos de usuarios
        $userModel = new User();
        $rows = $userModel->toList();

        // Crear PDF orientado en vertical A4
        $pdf = new \CustomPDF('P', 'mm', 'A4');
        $pdf->AliasNbPages();
        $pdf->setReporteTitulo('Reporte de Usuarios');
        $pdf->AddPage();

        // Encabezados de tabla
        $headers = ['ID', 'Email', 'Descripción', 'Estado', 'Creación'];
        // Anchos que suman el ancho útil (aprox 180mm con márgenes de 15mm)
        $widths = [15, 60, 55, 20, 30];
        $pdf->TablaHeader($headers, $widths, 9);

        // Filas
        foreach ($rows as $r) {
            $pdf->SetX(\CustomPDF::MARGEN_X);
            $pdf->Cell($widths[0], 7, (string)$r->id, 1, 0, 'C');
            $pdf->Cell($widths[1], 7, iconv('UTF-8','windows-1252', (string)$r->email), 1, 0, 'L');
            $pdf->Cell($widths[2], 7, iconv('UTF-8','windows-1252', (string)($r->descripcion ?? '')), 1, 0, 'L');
            $pdf->Cell($widths[3], 7, iconv('UTF-8','windows-1252', (string)($r->estado ?? '')), 1, 0, 'C');
            $pdf->Cell($widths[4], 7, iconv('UTF-8','windows-1252', (string)($r->creacion ?? '')), 1, 1, 'C');
        }

        // Salida del PDF al navegador
        $pdf->Output('I', 'reporte_usuarios.pdf');
        exit();
    }
}
?>
