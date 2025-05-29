<?php
// reporte_seguidores.php
require_once 'conex.php';

if (!isset($_GET['usuarioID'], $_GET['formato'])) {
    die('Parámetros incompletos');
}

$idUsuario = intval($_GET['usuarioID']);
$formato   = $_GET['formato'];

// 1. Consulta para el correcto funcionamiento de la view en reporte de seguidores
$sql = "
  SELECT 
    nombre_seguidor,
    fecha_seguimiento
  FROM Vista_SeguidoresConNombre
  WHERE SeguidoID = ?
  ORDER BY fecha_seguimiento DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result();

// Si sí hay resultados, los procesamos
if ($formato === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=mis_seguidores.csv');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Nombre completo de tu seguidor','Fecha y Hora de seguimiento']);
    while ($row = $result->fetch_assoc()) {
        fputcsv($out, [
            $row['nombre_seguidor'],
            $row['fecha_seguimiento'],
        ]);
    }
    fclose($out);
    exit;
}

if ($formato === 'pdf') {
    require_once 'fpdf/fpdf.php';
    $pdf = new FPDF('P','mm','A4');
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(0,10,'Reporte de Mis Seguidores',0,1,'C');

    $pdf->SetFont('Arial','B',10);
    foreach (['Nombre completo de tu seguidor', 'Fecha y Hora de seguimiento'] as $heading) {
        $pdf->Cell(95,9,$heading,1);
    }
    $pdf->Ln();

    $pdf->SetFont('Arial','',9);
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(95,8,$row['nombre_seguidor'],1);
        $pdf->Cell(95,8,$row['fecha_seguimiento'],1);
        $pdf->Ln();
    }
    $pdf->Output('D','mis_seguidores.pdf');
    exit;
}

echo "Formato no soportado";
