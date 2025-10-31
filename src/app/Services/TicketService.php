<?php

namespace App\Services;

use FPDF;
use App\Models\Venta;
use App\Models\Sucursal;
use App\Models\User;

class TicketService
{
    protected $baseImagePath;

    public function __construct()
    {
        $this->baseImagePath = public_path('images/tickets/');
    }

    public function generateTicket(Venta $venta, Sucursal $sucursal, User $usuario)
    {
        // Obtener paths de logo y QR
        $logoPath = $this->getLogoPath($sucursal);
        $qrPath = $this->getQrPath($sucursal);
        
        // Calcular altura dinámica basada en productos
        $productosCount = $venta->detalles->count();
        $lineHeight = 5;
        $baseHeight = 120;
        $productosHeight = $productosCount * $lineHeight;
        $totalHeight = max(280, $baseHeight + $productosHeight);
        
        // Calcular totales de pagos
        $pagoEfectivo = 0;
        $pagoTransferencia = 0;
        $pagoTarjeta = 0;
        $transferenciaPara = 'N/A';

        foreach ($venta->pagos as $pago) {
            switch ($pago->metodo_pago) {
                case 'efectivo':
                    $pagoEfectivo += $pago->monto;
                    break;
                case 'transferencia':
                    $pagoTransferencia += $pago->monto;
                    $transferenciaPara = $pago->destinatario_transferencia ?? 'N/A';
                    break;
                case 'tarjeta':
                    $pagoTarjeta += $pago->monto;
                    break;
            }
        }

        // Crear instancia de FPDF
        $pdf = new FPDF('P', 'mm', array(72.1, $totalHeight));
        $pdf->SetMargins(5, 5, 5);
        $pdf->AddPage();
        
        // Centrar logo
        $pdfWidth = $pdf->GetPageWidth();
        $logoWidth = 40;
        $centerX = ($pdfWidth - $logoWidth) / 2;
        
        if (file_exists($logoPath)) {
            $pdf->Image($logoPath, $centerX, 5, $logoWidth);
        }
        
        // Encabezado de la tienda - usar datos directamente de la sucursal
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Ln(35);
        $pdf->Cell(0, 5, iconv('UTF-8', 'windows-1252', $sucursal->nombre), 0, 1, 'C');
        $pdf->SetFont('Arial', '', 8);
        $pdf->MultiCell(0, 5, iconv('UTF-8', 'windows-1252', $sucursal->direccion), 0, 'C');
        $pdf->Cell(0, 5, 'AGUASCALIENTES, AGUASCALIENTES', 0, 1, 'C');
        
        // Información del ticket
        $pdf->Ln(3);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(30, 5, 'Ticket:', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(30, 5, $venta->id, 0, 1, 'L');
        
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(30, 5, 'Fecha:', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(30, 5, $venta->fecha->format('d/m/Y H:i:s'), 0, 1, 'L');
        
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(30, 5, 'Vendedor:', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(30, 5, iconv('UTF-8', 'windows-1252', $usuario->nombre), 0, 1, 'L');
        
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(30, 5, 'Cliente:', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 8);
        $clientName = $venta->cliente ? $venta->cliente->nombre : 'Cliente General';
        $pdf->Cell(30, 5, iconv('UTF-8', 'windows-1252', $clientName), 0, 1, 'L');
        
        // Tabla de productos
        $pdf->Ln(3);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(27, 5, 'Producto', 1);
        $pdf->Cell(10, 5, 'Cant.', 1, 0, 'C');
        $pdf->Cell(13, 5, 'P. U.', 1, 0, 'C');
        $pdf->Cell(15, 5, 'Total', 1, 1, 'C');
        $pdf->SetFont('Arial', '', 8);
        
        $totalGeneral = 0;
        foreach ($venta->detalles as $detalle) {
            $nombreProducto = iconv('UTF-8', 'windows-1252', $detalle->producto->nombre);
            $cantidad = $detalle->cantidad;
            $precioUnitario = number_format($detalle->precio_unitario, 2, '.', ',');
            $precioTotal = number_format($detalle->total_linea, 2, '.', ',');
            $totalGeneral += $detalle->total_linea;
            
            $pdf->Cell(27, 5, $nombreProducto, 1, 0, 'C');
            $pdf->Cell(10, 5, $cantidad, 1, 0, 'C');
            $pdf->Cell(13, 5, '$' . $precioUnitario, 1, 0, 'C');
            $pdf->Cell(15, 5, '$' . $precioTotal, 1, 1, 'C');
        }
        
        // Total general
        $pdf->SetFont('Arial', 'B', 8);
        $precioTotalGeneral = number_format($totalGeneral, 2, '.', ',');
        $pdf->Cell(37, 5, 'Total General:', 1, 0, 'R');
        $pdf->Cell(28, 5, '$' . $precioTotalGeneral, 1, 1, 'R');
        
        // Detalles de pago
        $pdf->Ln(3);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(0, 5, 'Detalles de Pago', 0, 1, 'L');
        $pdf->SetFont('Arial', '', 8);
        
        $pdf->Cell(0, 5, 'Efectivo: $' . number_format($pagoEfectivo, 2, '.', ','), 0, 1, 'L');
        $pdf->Cell(0, 5, 'Transferencia: $' . number_format($pagoTransferencia, 2, '.', ','), 0, 1, 'L');
        
        if ($pagoTransferencia > 0) {
            $pdf->Cell(0, 5, 'Transferencia a: ' . $transferenciaPara, 0, 1, 'L');
        }
        
        if ($pagoTarjeta > 0) {
            $pdf->Cell(0, 5, 'Tarjeta: $' . number_format($pagoTarjeta, 2, '.', ','), 0, 1, 'L');
        }
        
        // QR y pie de página
        $pdf->Ln(3);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(0, 5, iconv('UTF-8', 'windows-1252', 'Únete a nuestro grupo de WhatsApp'), 0, 1, 'C');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(0, 5, iconv('UTF-8', 'windows-1252', 'y descubre nuestros nuevos modelos cada semana.'), 0, 1, 'C');
        
        if (file_exists($qrPath)) {
            $qrWidth = 30;
            $centerX = ($pdfWidth - $qrWidth) / 2;
            $pdf->Image($qrPath, $centerX, $pdf->GetY(), $qrWidth);
        }
        
        $pdf->Ln(35);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(0, 5, iconv('UTF-8', 'windows-1252', '¡Gracias por tu compra!'), 0, 1, 'C');
        $pdf->Cell(0, 5, 'Visitanos nuevamente.', 0, 1, 'C');
        
        // Generar PDF
        $pdf->Output('I', 'Ticket_Venta_' . $venta->id . '.pdf');
        exit;
    }
    
private function getLogoPath(Sucursal $sucursal)
{
    // Si la sucursal tiene un logo específico configurado en la BD, usarlo
    if ($sucursal->logo_path) {
        $logoPath = public_path($sucursal->logo_path);
        if (file_exists($logoPath)) {
            return $logoPath;
        }
    }

    // Lógica de respaldo basada en el nombre de la sucursal
    $sucursalNombre = strtolower($sucursal->nombre);
    
    $logos = [
        'matriz' => 'logos/elprogreso.png',
        'mariano1' => 'logos/logo_freshboys.png',
        'mariano2' => 'logos/logo_freshboys.png',
        'mariano3' => 'logos/logo_freshboys.png',
        'centro' => 'logos/logo_freshboys.png',
        'fresh2' => 'logos/logo_freshboys.png',
        'pilar' => 'logos/logo_freshhype2.png',
        'society' => 'logos/society2.png',
        'sbhype' => 'logos/logo_sbhype2.png'
    ];

    // Buscar coincidencias en el nombre de la sucursal
    foreach ($logos as $key => $logoFile) {
        if (str_contains($sucursalNombre, $key)) {
            $fullPath = $this->baseImagePath . $logoFile;
            if (file_exists($fullPath)) {
                return $fullPath;
            }
        }
    }

    // Logo por defecto si no se encuentra ninguno
    $defaultPath = $this->baseImagePath . 'logos/logo_default.png';
    return file_exists($defaultPath) ? $defaultPath : null;
}

private function getQrPath(Sucursal $sucursal)
{
    // Si la sucursal tiene un QR específico configurado en la BD, usarlo
    if ($sucursal->qr_path) {
        $qrPath = public_path($sucursal->qr_path);
        if (file_exists($qrPath)) {
            return $qrPath;
        }
    }

    // Lógica de respaldo basada en el nombre de la sucursal
    $sucursalNombre = strtolower($sucursal->nombre);
    
    // Para sbhype usar QR especial, para otros el default
    $qrFile = (str_contains($sucursalNombre, 'sbhype')) ? 'qr/qr_sbhype.jpeg' : 'qr/qr_default.jpeg';
    
    $fullPath = $this->baseImagePath . $qrFile;
    
    // Si no existe el QR específico, usar el default
    if (!file_exists($fullPath)) {
        $fullPath = $this->baseImagePath . 'qr/qr_default.jpeg';
    }
    
    return file_exists($fullPath) ? $fullPath : null;
}
}