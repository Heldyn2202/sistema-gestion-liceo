<?php
// Incluir archivos de configuración y librería TCPDF
include('../../app/config.php');
require_once 'library/tcpdf.php';

// Manejo de errores básico
try {
    // Obtener el periodo académico activo
    $sql_gestiones = "SELECT * FROM gestiones WHERE estado = 1 ORDER BY desde DESC LIMIT 1";
    $query_gestiones = $pdo->prepare($sql_gestiones);
    $query_gestiones->execute();
    $gestion_activa = $query_gestiones->fetch(PDO::FETCH_ASSOC);

    if (!$gestion_activa) {
        throw new Exception("No se encontró un periodo académico activo.");
    }

    // Obtener el grado seleccionado
    $grado_seleccionado = isset($_GET['grado']) ? $_GET['grado'] : null;

    // Consulta para obtener la lista de estudiantes inscritos filtrados por grado y periodo académico activo
    $sql_inscripciones = "SELECT e.cedula, e.nombres, e.apellidos, e.genero, i.turno_id, g.grado, i.nombre_seccion
                          FROM inscripciones i
                          INNER JOIN estudiantes e ON i.id_estudiante = e.id_estudiante
                          INNER JOIN grados g ON i.grado = g.id_grado
                          WHERE i.id_gestion = :id_gestion";

    if ($grado_seleccionado) {
        $sql_inscripciones .= " AND g.grado = :grado";
    }

    $stmt = $pdo->prepare($sql_inscripciones);
    $stmt->bindParam(':id_gestion', $gestion_activa['id_gestion']);

    if ($grado_seleccionado) {
        $stmt->bindParam(':grado', $grado_seleccionado);
    }

    $stmt->execute();
    $inscripciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Crear nuevo PDF
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Ministerio de Educación');
    $pdf->SetTitle('Reporte de Matrícula Escolar');
    $pdf->SetSubject('Reporte de Matrícula');
    $pdf->SetKeywords('TCPDF, PDF, matrícula, escolar');

    // Eliminar encabezado y pie de página predeterminados
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    $pdf->SetMargins(15, 30, 15); // Márgenes más amplios
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->AddPage();

    // Agregar dos logos en la parte superior
    $pdf->Image('logos/logo.png', 15, 10, 20, 0, 'PNG', '', '', true, 300, '', false, false, 0, false, false, false); // Logo izquierdo
    $pdf->Image('logos/ministerio.jpg', 150, 10, 40, 0, 'JPG', '', '', true, 300, '', false, false, 0, false, false, false); // Logo derecho (mismo tamaño)

    
    // Título del documento
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'U.E.N ROBERTO MARTINEZ CENTENO', 0, 1, 'C');
    $pdf->SetFont('helvetica', '', 14);
    $pdf->Cell(0, 10, 'Periodo Académico: ' . date('Y', strtotime($gestion_activa['desde'])) . ' - ' . date('Y', strtotime($gestion_activa['hasta'])), 0, 1, 'C');

    // Establecer el contenido del PDF
    $html = '<style>
                h1 { text-align: center; color: #004080; font-weight: bold; font-size: 16px; margin-bottom: 10px; }
                h4 { color: #004080; font-weight: bold; font-size: 14px; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                th { background-color: #004080; color: white; font-weight: bold; padding: 8px; text-align: center; }
                td { padding: 8px; border: 1px solid #ddd; text-align: center; }
                .total-row { background-color: #f2f2f2; font-weight: bold; }
                .title-box { background-color: #004080; color: white; padding: 10px; text-align: center; font-size: 16px; font-weight: bold; margin-bottom: 20px; }
            </style>';

    // Título llamativo
    $html .= '<div class="title-box">Reporte de Matrícula Escolar</div>';

    $html .= '<h4></h4>';
    $html .= '<table>
                <tr>
                    <th>Grado</th>
                    <th>Sección</th>
                    <th>Total Masculino</th>
                    <th>Total Femenino</th>
                </tr>';

    // Agrupar por grado y sección
    $grados_secciones = [];
    foreach ($inscripciones as $inscripcion) {
        $grado = $inscripcion['grado'];
        $seccion = $inscripcion['nombre_seccion'];

        if (!isset($grados_secciones[$grado][$seccion])) {
            $grados_secciones[$grado][$seccion] = ['masculino' => 0, 'femenino' => 0];
        }

        if (strtolower($inscripcion['genero']) == 'masculino') {
            $grados_secciones[$grado][$seccion]['masculino']++;
        } elseif (strtolower($inscripcion['genero']) == 'femenino') {
            $grados_secciones[$grado][$seccion]['femenino']++;
        }
    }

    // Imprimir grados y secciones
    foreach ($grados_secciones as $grado => $secciones) {
        foreach ($secciones as $seccion => $totales) {
            $html .= '<tr>
                        <td>' . htmlspecialchars($grado) . '</td>
                        <td>' . htmlspecialchars($seccion) . '</td>
                        <td>' . htmlspecialchars($totales['masculino']) . '</td>
                        <td>' . htmlspecialchars($totales['femenino']) . '</td>
                      </tr>';
        }
    }

    $html .= '</table>';

    // Agregar sección de matrícula
    $html .= '<h3></h3>';
    $html .= '<table>
                <tr>
                    <th>Género</th>
                    <th>Total</th>
                </tr>';

    // Inicializar contadores para la sección de matrícula
    $total_masculino = 0;
    $total_femenino = 0;

    // Sumar los totales de masculinos y femeninos
    foreach ($grados_secciones as $secciones) {
        foreach ($secciones as $totales) {
            $total_masculino += $totales['masculino'];
            $total_femenino += $totales['femenino'];
        }
    }

    $html .= '<tr class="total-row">
                <td>Masculino</td>
                <td>' . $total_masculino . '</td>
              </tr>
              <tr class="total-row">
                <td>Femenino</td>
                <td>' . $total_femenino . '</td>
              </tr>
              <tr class="total-row">
                <td>Total</td>
                <td>' . ($total_masculino + $total_femenino) . '</td>
              </tr>
              </table>';

    // Escribir el contenido al PDF
    $pdf->writeHTML($html, true, false, true, false, '');
    // Agregar el texto centrado en la parte inferior
    $pdf->SetY(-40); // Ajustar posición vertical
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Dirección - Subdirección', 0, 1, 'C', 0, '', 0);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 3, '________________________________________________', 0, 1, 'C', 0, '', 0);

    // Cerrar y generar el PDF
    $pdf->Output('reporte_matricula.pdf', 'D');

} catch (Exception $e) {
    // Manejo de errores
    echo "Error: " . $e->getMessage();
}
?>