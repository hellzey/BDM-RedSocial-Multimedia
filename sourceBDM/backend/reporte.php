<?php
// reporte.php
require_once 'conex.php';

if (!isset($_GET['usuarioID'], $_GET['formato'])) {
    die('Parámetros incompletos');
}

$idUsuario = intval($_GET['usuarioID']);
$formato   = $_GET['formato'];

// 1. Obtener datos
$sql = "
  SELECT 
    p.publiID, 
    p.descripcion, 
    p.fechacreacion,
    COALESCE(r.total_reacciones, 0) AS total_reacciones,
    COALESCE(c.total_comentarios, 0) AS total_comentarios
  FROM Publicaciones p
  LEFT JOIN (
    SELECT publiID, COUNT(*) AS total_reacciones
    FROM Reacciones
    GROUP BY publiID
  ) r ON p.publiID = r.publiID
  LEFT JOIN (
    SELECT publiID, COUNT(*) AS total_comentarios
    FROM Comentarios
    GROUP BY publiID
  ) c ON p.publiID = c.publiID
  WHERE p.usuarioID = ?
  ORDER BY total_reacciones DESC, total_comentarios DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result();

// 2A. Generar CSV
if ($formato === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=mis_publicaciones.csv');
    $out = fopen('php://output', 'w');
    // Cabecera
    fputcsv($out, ['ID Publicacion','Descripcion','Fecha y Hora','Reacciones', 'Comentarios']);
    while ($row = $result->fetch_assoc()) {
        fputcsv($out, [
            $row['publiID'],
            $row['descripcion'],
            $row['fechacreacion'],
            $row['total_reacciones'],
            $row['total_comentarios']
        ]);
    }
    fclose($out);
    exit;
}

// 2B. Generar PDF (usando FPDF)
if ($formato === 'pdf') {
    require_once 'fpdf/fpdf.php'; // Asegúrate de tener la librería
    $pdf = new FPDF('P','mm','A4');
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(0,10,'Reporte de Mis Publicaciones',0,1,'C');
    // Encabezados
    $pdf->SetFont('Arial','B',10);
    foreach (['ID','Descripcion','Fecha y Hora','Reacciones','Comentarios'] as $heading) {
        $pdf->Cell(38,9,$heading,1);
    }
    $pdf->Ln();
    // Datos
    $pdf->SetFont('Arial','',9);
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(38,8,$row['publiID'],1);
        $pdf->Cell(38,8,substr($row['descripcion'],0,20).'...',1);
        $pdf->Cell(38,8,$row['fechacreacion'],1);
        $pdf->Cell(38,8,$row['total_reacciones'],1);
        $pdf->Cell(38,8,$row['total_comentarios'],1);
        $pdf->Ln();
    }
    $pdf->Output('D','mis_publicaciones.pdf');
    exit;
}

echo "Formato no soportado";
