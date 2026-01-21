<?php
// Incluye la librería FPDF base. Ajusta la ruta si es necesario.
require_once(ROOT . 'Vendor' . DS . 'fpdf' . DS . 'fpdf.php');

class CustomPDF extends FPDF {
    
    // Propiedad para almacenar el título específico del reporte (ej. "REPORTE DE USUARIOS")
    public $reporteTitulo;
    
    // Constantes para definir el formato estándar
    const MARGEN_X = 15;
    const MARGEN_Y = 20;
    
    // --- Configuración Inicial ---
    
    public function setReporteTitulo($titulo) {
        $this->reporteTitulo = $titulo;
    }

    // --- 1. ENCABEZADO DE PÁGINA (Header) ---
    function Header() {
        // Establecer márgenes para toda la página
        $this->SetMargins(self::MARGEN_X, self::MARGEN_Y, self::MARGEN_X);

        // 1. Configuración de Fuente Estándar
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor(0, 0, 0); // Color Azul Oscuro

        // 2. LOGO IZQUIERDO (Coordenadas: X, Y, Ancho)
        // Ruta absoluta: C:\xamppWEB\htdocs\TicketPro_copia\Content\Demo\img\logo1.png
        // Asumiendo que el script se ejecuta desde la raíz o que la ruta es correcta
        $this->Image('Content/Demo/img/logo_bac_reporte.png', 15, 14, 40); 

        // 3. LOGO DERECHO
        // (Ancho de la página 210mm - ancho del logo 30mm - margen derecho 10mm) = 170
        $this->Image('Content/Demo/img/logo_tp_reporte.png', 155, 15, 40); 

        // 4. INFORMACIÓN CENTRAL (Celda con 100mm de ancho)
        $this->SetXY(55, 10); // Posiciona el cursor en X=55, Y=10
        
        $infoText = "Bac Credomatic - TicketPro\nTel. 9999-9900\nMail. baccredomatic@bac.hn\nwww.baccredomatic.com\nOficina Principal Tegucigalpa, Honduras";
        
        // Convertir el texto a ISO-8859-1 para manejar tildes/ñ si FPDF no usa UTF-8
        $this->MultiCell(100, 4, iconv('UTF-8', 'windows-1252', $infoText), 0, 'C');

        // 5. TÍTULO DEL REPORTE (Debajo de la información central)
        $this->Ln(5); // Salto de línea después de la información central
        $this->SetFont('Arial', 'BU', 14); // Fuente: Arial, Negrita, Subrayada, 14pt
        
        // Mueve el cursor a la derecha para centrar mejor
        $this->Cell(25); 
        
        $this->Cell(120, 10, iconv('UTF-8', 'windows-1252', strtoupper($this->reporteTitulo)), 0, 1, 'C');
        $this->Ln(1);
        
        // 6. Línea Separadora y Salto de Espacio
        // $this->SetDrawColor(0, 51, 102); // Color de línea
        // $this->SetLineWidth(0.5);
        // $this->Line(self::MARGEN_X, $this->GetY(), 210 - self::MARGEN_X, $this->GetY());
        // $this->Ln(5); // Espacio antes de la tabla

        // IMPORTANTE: Asegúrate de que el método Header() termine con SetY() 
        // o Ln() para posicionar la tabla correctamente.
    }

    // --- 2. PIE DE PÁGINA (Footer) ---
    function Footer() {
        // Posición: 1.5 cm desde el fondo
        $this->SetY(-17);
        
        // Configuración de Fuente y Color
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(128, 128, 128); // Gris

        // 1. TEXTO IZQUIERDO (Bac Credomatic - TicketPro)
        // Cell(ancho 0 = hasta el final, alto, texto, borde, posición después, alineación)
        $this->Cell(95, 10, iconv('UTF-8', 'windows-1252', 'Bac Credomatic - TicketPro'), 0, 0, 'L');
        
        // 2. NÚMERO DE PÁGINA (Lado derecho)
        $this->Cell(95, 10, 'Página ' . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    // --- 3. MÉTODO PARA DIBUJAR ENCABEZADOS DE TABLA (Reutilizable) ---
    public function TablaHeader($titulos, $anchos, $fuente=9) {
        $this->SetX(self::MARGEN_X);
        
        $this->SetFillColor(255, 110, 81); // Color de fondo de columnas
        $this->SetTextColor(0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.3);
        $this->SetFont('Arial', 'B', $fuente); // Fuente del encabezado

        for($i = 0; $i < count($titulos); $i++) {
            // Convierte el texto para soportar tildes/ñ
            $texto = iconv('UTF-8', 'windows-1252', $titulos[$i]);
            $this->Cell($anchos[$i], 7, $texto, 1, 0, 'C', true);
        }
        $this->Ln();
        // Restablecer fuente para los datos
        $this->SetFont('Arial', '', $fuente); 
    }
}
?>