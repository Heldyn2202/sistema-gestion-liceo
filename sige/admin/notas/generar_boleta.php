<?php
include('../../app/config.php');
require_once('library/tcpdf.php');

// 1. Configuración de la librería QR Code
$qrLibPath = 'library/phpqrcode/qrlib.php';
if (!file_exists($qrLibPath)) {
    if (!is_dir('library/phpqrcode')) {
        mkdir('library/phpqrcode', 0755, true);
    }
    $qrLibContent = file_get_contents('https://raw.githubusercontent.com/t0k4rt/phpqrcode/master/qrlib.php');
    file_put_contents($qrLibPath, $qrLibContent);
}
require_once($qrLibPath);

try {
    // 2. Validación del ID del estudiante
    if (!isset($_GET['id_estudiante']) || !is_numeric($_GET['id_estudiante'])) {
        throw new Exception("ID de estudiante no válido");
    }
    $id_estudiante = $_GET['id_estudiante'];
    
    // 3. Obtener parámetros de lapsos
    $lapsos_seleccionados = isset($_GET['lapsos']) && !empty($_GET['lapsos']) ? 
                           explode(',', $_GET['lapsos']) : [];
    $incluir_final = isset($_GET['final']) && $_GET['final'] == '1';
    
    // Validar que al menos haya un lapso seleccionado o el promedio final
    if (empty($lapsos_seleccionados) && !$incluir_final) {
        throw new Exception("Debe seleccionar al menos un lapso o el promedio final");
    }

    // 4. Obtener datos del estudiante
    $sql_estudiante = "SELECT e.nombres, e.apellidos, e.cedula, 
                      g.grado as nombre_grado, i.nombre_seccion
                      FROM estudiantes e
                      JOIN inscripciones i ON e.id_estudiante = i.id_estudiante
                      JOIN grados g ON i.grado = g.id_grado
                      WHERE e.id_estudiante = ? LIMIT 1";
    
    $stmt_estudiante = $pdo->prepare($sql_estudiante);
    $stmt_estudiante->execute([$id_estudiante]);
    $estudiante = $stmt_estudiante->fetch(PDO::FETCH_ASSOC);

    if (!$estudiante) {
        throw new Exception("Estudiante no encontrado");
    }

    // 5. Obtener información de todos los lapsos (primero, segundo, tercero)
    $sql_lapsos = "SELECT * FROM lapsos ORDER BY fecha_inicio";
    $stmt_lapsos = $pdo->prepare($sql_lapsos);
    $stmt_lapsos->execute();
    $lapsos_info = $stmt_lapsos->fetchAll(PDO::FETCH_ASSOC);

    // 6. Obtener calificaciones del estudiante
    $sql_notas = "SELECT m.nombre_materia, ne.calificacion, l.nombre_lapso, l.id_lapso
                  FROM notas_estudiantes ne
                  JOIN materias m ON ne.id_materia = m.id_materia
                  JOIN lapsos l ON ne.id_lapso = l.id_lapso
                  WHERE ne.id_estudiante = ?";
    
    $params_notas = [$id_estudiante];
    
    if (!empty($lapsos_seleccionados)) {
        $placeholders = implode(',', array_fill(0, count($lapsos_seleccionados), '?'));
        $sql_notas .= " AND ne.id_lapso IN ($placeholders)";
        $params_notas = array_merge($params_notas, $lapsos_seleccionados);
    }
    
    $stmt_notas = $pdo->prepare($sql_notas);
    $stmt_notas->execute($params_notas);
    $notas = $stmt_notas->fetchAll(PDO::FETCH_ASSOC);

    // Validar que haya calificaciones si se seleccionaron lapsos
    if (empty($notas) && !empty($lapsos_seleccionados)) {
        throw new Exception("No hay calificaciones registradas para los lapsos seleccionados");
    }

    // Organizar notas por materia y luego por lapso
    $notas_por_materia = [];
    foreach ($notas as $nota) {
        $materia = $nota['nombre_materia'];
        if (!isset($notas_por_materia[$materia])) {
            $notas_por_materia[$materia] = [];
        }
        $notas_por_materia[$materia][$nota['id_lapso']] = $nota['calificacion'];
    }

    // 7. Crear PDF en orientación horizontal
    $pdf = new TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);
    $pdf->SetCreator('Sistema Académico');
    $pdf->SetAuthor('U.E.N. Roberto Martinez Centeno');
    $pdf->SetTitle('Boleta de Calificaciones');
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetAutoPageBreak(TRUE, 20);
    $pdf->AddPage();

    // 8. Encabezado del documento optimizado
    $pdf->Rect(0, 0, 297, 30, 'F'); // 297mm es el ancho de A4 horizontal
    
    // Logos institucionales
    if (file_exists('logos/logo.png')) {
        $pdf->Image('logos/logo.png', 15, 8, 20, 0, 'PNG', '', 'T', false, 300);
    }
    if (file_exists('logos/ministerio.png')) {
        $pdf->Image('logos/ministerio.png', 250, 8, 30, 0, 'PNG', '', '', true, 300, '', false, false, 0, false, false, false);
    }
    
    // Texto del encabezado
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetY(8);
    $pdf->Cell(0, 5, 'REPÚBLICA BOLIVARIANA DE VENEZUELA', 0, 1, 'C');
    $pdf->Cell(0, 5, 'MINISTERIO DEL PODER POPULAR PARA LA EDUCACIÓN', 0, 1, 'C');
    $pdf->Cell(0, 5, 'U.E.N. ROBERTO MARTINEZ CENTENO', 0, 1, 'C');
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 5, 'BOLETA DE CALIFICACIONES', 0, 1, 'C');

    // Línea divisoria
    $pdf->SetLineStyle(['width' => 0.5, 'color' => [0, 0, 0]]);
    $pdf->Line(15, 30, 282, 30);

    // 9. Datos del estudiante (más compacto)
    $pdf->SetY(35);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetFillColor(220, 230, 240);
    $pdf->Cell(0, 8, 'DATOS DEL ESTUDIANTE', 0, 1, 'C', true);
    
    $html = '<style>
                .student-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 10px; }
                .student-table th { background-color: #e6f2ff; padding: 5px; text-align: left; border: 1px solid #cce0ff; }
                .student-table td { padding: 5px; border: 1px solid #e6f2ff; }
            </style>';
    
    $html .= '<table class="student-table">
                <tr>
                    <th width="30%">Nombre Completo</th>
                    <td width="70%">' . htmlspecialchars($estudiante['nombres'] . ' ' . $estudiante['apellidos']) . '</td>
                </tr>
                <tr>
                    <th>Cédula</th>
                    <td>' . htmlspecialchars($estudiante['cedula']) . '</td>
                </tr>
                <tr>
                    <th>Grado/Sección</th>
                    <td>' . htmlspecialchars($estudiante['nombre_grado'] . ' - ' . $estudiante['nombre_seccion']) . '</td>
                </tr>
              </table>';
    
    $pdf->writeHTML($html, true, false, true, false, '');

    // 10. Calificaciones por lapso en columnas
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetFillColor(220, 230, 240);
    $pdf->Cell(0, 8, 'CALIFICACIONES POR LAPSO', 0, 1, 'C', true);

    // Crear tabla de calificaciones
    $html = '<table style="width: 100%; border-collapse: collapse; font-size: 11px; margin-bottom: 10px;">';
    
    // Encabezados de columnas
    $html .= '<tr>
                <th style="background-color: #145388; color: white; padding: 5px; text-align: center; width: 40%;">MATERIAS</th>';
    
    foreach ($lapsos_info as $lapso) {
        $html .= '<th style="background-color: #145388; color: white; padding: 5px; text-align: center; width: 20%;">' . strtoupper($lapso['nombre_lapso']) . '</th>';
    }
    
    $html .= '</tr>';
    
    // Filas de materias y notas
    foreach ($notas_por_materia as $materia => $notas_lapsos) {
        $html .= '<tr>';
        $html .= '<td style="padding: 5px; border: 1px solid #e6f2ff; text-align: center;">' . htmlspecialchars($materia) . '</td>';
        
        foreach ($lapsos_info as $lapso) {
            $id_lapso = $lapso['id_lapso'];
            $nota = isset($notas_lapsos[$id_lapso]) ? number_format($notas_lapsos[$id_lapso], 2) : '-';
            
            // Determinar color según la calificación
            if ($nota !== '-') {
                $valor_nota = $notas_lapsos[$id_lapso];
                if ($valor_nota >= 19) {
                    $bgColor = '#e6f2ff'; // Excelente
                } elseif ($valor_nota >= 16) {
                    $bgColor = '#e6f2ff'; // Muy Bueno
                } elseif ($valor_nota >= 10) {
                    $bgColor = '#e6f2ff'; // Regular
                } else {
                    $bgColor = '#e6f2ff'; // Reprobado
                }
            } else {
                $bgColor = '#ffffff';
            }
            
            $html .= '<td style="padding: 5px; border: 1px solid #e6f2ff; text-align: center; background-color: ' . $bgColor . ';">' . $nota . '</td>';
        }
        
        $html .= '</tr>';
    }
    
    $html .= '</table>';
    $pdf->writeHTML($html, true, false, true, false, '');

    // 11. Promedio final (si está seleccionado)
    if ($incluir_final && !empty($notas)) {
        // Calcular promedio general
        $suma = 0;
        $count = 0;
        foreach ($notas as $nota) {
            $suma += $nota['calificacion'];
            $count++;
        }
        $promedio_final = $count > 0 ? $suma / $count : 0;
        
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetFillColor(200, 220, 240);
        $pdf->Cell(0, 8, 'PROMEDIO FINAL DEL AÑO', 0, 1, 'C', true);
        
        $html = '<table style="width: 100%; border-collapse: collapse; font-size: 10px;">
                    <tr>
                        <td style="padding: 5px; border: 1px solid #e6f2ff; text-align: center; font-weight: bold; background-color: #e8f5e9;">' . number_format($promedio_final, 2) . '</td>
                    </tr>
                </table>';
        
        $pdf->writeHTML($html, true, false, true, false, '');
    }

    // 12. Pie de página optimizado (con imágenes de firma)
$qrContent = 'ID:' . $id_estudiante . '|' . $estudiante['cedula'] . '|' . 
             $estudiante['nombres'] . ' ' . $estudiante['apellidos'] . '|' . 
             $estudiante['nombre_grado'] . ' ' . $estudiante['nombre_seccion'] . '|' . 
             date('Y-m-d');

$qrDir = 'temp_qr/';
if (!file_exists($qrDir)) mkdir($qrDir, 0755, true);
$qrFile = $qrDir . 'qr_' . $id_estudiante . '_' . time() . '.png';
QRcode::png($qrContent, $qrFile, QR_ECLEVEL_H, 6, 2);

// Ruta a las imágenes de firma (ajusta estas rutas según tu estructura de archivos)
$firmaProfesor = 'logos/blanco.png';
$firmaDirector = 'logos/blanco.png';

$html = '<div style="border-top: 1px solid #e6f2ff; padding-top: 10px; margin-top: 10px; font-size: 9px;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 15%; text-align: center; vertical-align: top;">
                        <img src="' . $qrFile . '" width="80" height="80" />
                        <div style="margin-top: 2px;">Código de verificación</div>
                    </td>
                    <td style="width: 70%; padding: 0 10px; vertical-align: bottom;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="width: 50%; text-align: center;">
                                    <!-- Firma del profesor con imagen -->
                                    <div style="margin-bottom: 5px;">';
                                    
// Añadir imagen de firma del profesor si existe
if (file_exists($firmaProfesor)) {
    $html .= '<img src="' . $firmaProfesor . '" style="height: 40px; margin-bottom: 5px;" />';
}

$html .= '                      </div>
                                    <div style="border-top: 1px solid #145388; width: 80%; margin: 0 auto; padding-top: 10px;">
                                        <div style="font-weight: bold;">Prof. Tutor/a</div>
                                        <div>Firma</div>
                                    </div>
                                </td>
                                <td style="width: 50%; text-align: center;">
                                    <!-- Firma del director con imagen -->
                                    <div style="margin-bottom: 5px;">';
                                    
// Añadir imagen de firma del director si existe
if (file_exists($firmaDirector)) {
    $html .= '<img src="' . $firmaDirector . '" style="height: 40px; margin-bottom: 5px;" />';
}

$html .= '                      </div>
                                    <div style="border-top: 1px solid #145388; width: 80%; margin: 0 auto; padding-top: 10px;">
                                        <div style="font-weight: bold;">Director/a</div>
                                        <div>Firma y Sello</div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>';

$pdf->writeHTML($html, true, false, true, false, '');

    // 13. Limpieza y salida
    if (file_exists($qrFile)) unlink($qrFile);
    $filename = 'Boleta_' . str_replace(' ', '_', $estudiante['nombres']) . '_' . 
                str_replace(' ', '_', $estudiante['apellidos']) . '.pdf';
    $pdf->Output($filename, 'D');

} catch (Exception $e) {
    // Manejo de errores
    $error_message = "¡Ocurrió un error!";
    
    if (ini_get('display_errors')) {
        $error_message .= "<br><br>Detalles: " . htmlspecialchars($e->getMessage());
    }
    
    echo '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; border: 1px solid #e74c3c; border-radius: 5px; background-color: #fdecea; color: #c0392b;">
            <h3 style="color: #e74c3c; margin-top: 0;">' . $error_message . '</h3>
            <p style="font-size: 14px; color: #7f8c8d;">Por favor, intenta nuevamente o contacta al departamento de sistemas.</p>
          </div>';
}
?>