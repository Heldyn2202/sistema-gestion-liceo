<?php  
ob_start();  
include('../../app/config.php');  

$id_estudiante = isset($_GET['id_estudiante']) ? $_GET['id_estudiante'] : null;  

// Consulta ampliada para incluir más datos
$sql = "SELECT e.nombres, e.apellidos, e.fecha_nacimiento, e.tipo_cedula, e.cedula, e.cedula_escolar, 
               i.nivel_id, gr.grado, i.id_seccion, i.turno_id, g.desde, g.hasta, 
               r.nombres as rep_nombres, r.apellidos as rep_apellidos, r.cedula as rep_cedula
        FROM inscripciones i  
        JOIN estudiantes e ON i.id_estudiante = e.id_estudiante  
        JOIN gestiones g ON i.id_gestion = g.id_gestion  
        JOIN grados gr ON i.grado = gr.id_grado
        LEFT JOIN representantes r ON e.id_representante = r.id_representante
        WHERE i.id_estudiante = :id_estudiante AND g.estado = 1  
        ORDER BY i.created_at DESC  
        LIMIT 1";  
$stmt = $pdo->prepare($sql);  
$stmt->bindParam(':id_estudiante', $id_estudiante);  
$stmt->execute();  
$inscripcion = $stmt->fetch(PDO::FETCH_ASSOC);  


if ($inscripcion) {
    // Mostrar vista previa si no se ha confirmado la descarga
    if (!isset($_GET['confirmar'])) {
        echo '
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Vista previa - Constancia de Inscripción</title>
            <style>
                body { 
                    font-family: "Segoe UI", Arial, sans-serif; 
                    margin: 0; 
                    padding: 0; 
                    background: linear-gradient(135deg,rgb(36, 90, 172) 0%,rgb(105, 181, 240) 100%);
                    min-height: 100vh;
                }
                .container { 
                    max-width: 900px; 
                    margin: 30px auto; 
                    background: white; 
                    padding: 30px; 
                    border-radius: 10px; 
                    box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
                    position: relative;
                    overflow: hidden;
                }
                .container::before {
                    content: "";
                    position: absolute;
                    top: 0;
                    right: 0;
                    bottom: 0;
                    left: 0;
                    background: url("logos/agua.jpg") center/contain no-repeat;
                    opacity: 0.05;
                    z-index: 0;
                }
                .content {
                    position: relative;
                    z-index: 1;
                }
                h1 { 
                    color: #2c3e50; 
                    text-align: center; 
                    margin-bottom: 30px;
                    font-weight: 600;
                    border-bottom: 2px solid #3498db;
                    padding-bottom: 10px;
                }
                .preview-content { 
                    border: 1px solid #e0e0e0; 
                    padding: 30px; 
                    margin: 20px 0; 
                    background: white;
                    border-radius: 8px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
                }
                .preview-header {
                    text-align: center;
                    margin-bottom: 20px;
                }
                .preview-header h3 {
                    color: #2c3e50;
                    margin-bottom: 5px;
                }
                .preview-header hr {
                    width: 50%;
                    border: 0;
                    height: 1px;
                    background: linear-gradient(to right, transparent, #3498db, transparent);
                    margin: 10px auto;
                }
                .student-info {
                    margin-bottom: 25px;
                }
                .student-info p {
                    margin: 8px 0;
                    font-size: 16px;
                }
                .student-info strong {
                    color: #2c3e50;
                }
                .validation-steps {
                    background: #f8f9fa;
                    padding: 20px;
                    border-radius: 8px;
                    margin-top: 30px;
                    border-left: 4px solid #3498db;
                }
                .validation-steps h4 {
                    color: #2c3e50;
                    margin-top: 0;
                }
                .validation-steps ol {
                    padding-left: 20px;
                }
                .validation-steps li {
                    margin-bottom: 10px;
                    line-height: 1.5;
                }
                .buttons { 
                    text-align: center; 
                    margin-top: 30px;
                    display: flex;
                    justify-content: center;
                    gap: 15px;
                }
                .btn { 
                    padding: 12px 25px; 
                    border: none; 
                    border-radius: 6px; 
                    cursor: pointer; 
                    font-size: 16px;
                    font-weight: 500;
                    transition: all 0.3s ease;
                    text-decoration: none;
                    display: inline-block;
                }
                .btn-download { 
                    background-color: #2ecc71;
                    color: white; 
                }
                .btn-download:hover {
                    background-color: #27ae60;
                    transform: translateY(-2px);
                    box-shadow: 0 4px 8px rgba(46, 204, 113, 0.3);
                }
                .btn-cancel { 
                    background-color: #e74c3c; 
                    color: white; 
                }
                .btn-cancel:hover {
                    background-color: #c0392b;
                    transform: translateY(-2px);
                    box-shadow: 0 4px 8px rgba(231, 76, 60, 0.3);
                }
                .watermark {
                    color: #bdc3c7;
                    font-size: 12px;
                    text-align: center;
                    margin-top: 20px;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="content">
                    <h1>Vista previa de la Constancia de Inscripción</h1>
                    
                    <div class="preview-content">
                        <div class="preview-header">
                            <h3>CONSTANCIA DE INSCRIPCIÓN</h3>
                            <hr>
                        </div>
                        
                        <div class="student-info">
                            <p>Estudiante: <strong>'.mb_strtoupper($inscripcion['nombres'].' '.$inscripcion['apellidos']).'</strong></p>
                            <p>Fecha de Nacimiento: <strong>'.date('d/m/Y', strtotime($inscripcion['fecha_nacimiento'])).'</strong></p>
                            <p>Grado: <strong>'.mb_strtoupper($inscripcion['grado']).'</strong></p>
                            <p>Nivel: <strong>'.mb_strtoupper($inscripcion['nivel_id']).'</strong></p>
                            <p>Sección/Turno: <strong>'.mb_strtoupper($inscripcion['id_seccion']).' / '.mb_strtoupper($inscripcion['turno_id']).'</strong></p>
                            <p>Período Académico: <strong>'.date('Y', strtotime($inscripcion['desde'])).'-'.date('Y', strtotime($inscripcion['hasta'])).'</strong></p>
                            <p>Representante: <strong>'.mb_strtoupper($inscripcion['rep_nombres'].' '.$inscripcion['rep_apellidos']).'</strong></p>
                            <p>Cédula del Representante: <strong>V-'.$inscripcion['rep_cedula'].'</strong></p>
                        </div>
                        
                        <div class="validation-steps">
                            <h4>¿Cómo validar esta constancia?</h4>
                            <ol>
                                <li>La constancia incluirá un código QR único en la parte inferior derecha.</li>
                                <li>Para validar, escanee el código QR con cualquier aplicación lectora de códigos QR.</li>
                                <li>El código QR mostrará los datos del estudiante y la información de la inscripción.</li>
                                <li>Verifique que los datos mostrados coincidan con los de esta constancia.</li>
                                <li>Para mayor seguridad, puede presentar esta constancia en la institución para su verificación física.</li>
                                <li>Esta constancia tiene validez solo con el sello húmedo y firma original de la directora.</li>
                            </ol>
                            <p><strong>Nota:</strong> Cualquier alteración o modificación de este documento invalidará su autenticidad.</p>
                        </div>
                    </div>
                    
                    <div class="buttons">
                        <a href="?id_estudiante='.$id_estudiante.'&confirmar=1" class="btn btn-download">Generar PDF</a>
                        <a href="javascript:history.back()" class="btn btn-cancel">Cancelar</a>
                    </div>
                    
                    <div class="watermark">
                        U.E.N. Roberto Martínez Centeno - SIGE
                    </div>
                </div>
            </div>
        </body>
        </html>';
    } else {
        // Generar el PDF si el usuario ha confirmado
        require_once 'library/tcpdf.php';
        require_once 'library/phpqrcode/qrlib.php';
        
        // Datos para el QR
        $qrData = "CONSTANCIA DE INSCRIPCIÓN\n";
        $qrData .= "Estudiante: ".mb_strtoupper($inscripcion['nombres'].' '.$inscripcion['apellidos'])."\n";
        $qrData .= "Cédula: ".($inscripcion['tipo_cedula'] ? $inscripcion['tipo_cedula'].'-'.$inscripcion['cedula'] : $inscripcion['cedula_escolar'])."\n";
        $qrData .= "Grado: ".mb_strtoupper($inscripcion['grado'])."\n";
        $qrData .= "Nivel: ".mb_strtoupper($inscripcion['nivel_id'])."\n";
        $qrData .= "Fecha: ".date('d/m/Y')."\n";
        $qrData .= "U.E.N ROBERTO MARTINEZ CENTENO";
        
        // Generar QR temporal
        $qrTempFile = tempnam(sys_get_temp_dir(), 'qr');
        QRcode::png($qrData, $qrTempFile, QR_ECLEVEL_L, 5);
        
        $meses = [
            1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril', 5 => 'mayo', 6 => 'junio',
            7 => 'julio', 8 => 'agosto', 9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
        ];
        $mes_actual = $meses[date('n')];
        $dia_actual = date('d');
        $ultimo_dia_mes = date('t');

        $pdf = new TCPDF('P', 'mm', 'LETTER', true, 'UTF-8', false);  
        $pdf->SetCreator(PDF_CREATOR);  
        $pdf->SetAuthor('U.E. Roberto Martínez Centeno');  
        $pdf->SetTitle('Constancia de Inscripción');  
        $pdf->SetMargins(15, 15, 15);  
        $pdf->SetPrintHeader(false);  
        $pdf->SetPrintFooter(false);  
        $pdf->AddPage();  

        // Marco decorativo
        $pdf->Rect(10, 10, 190, 260, 'D', array('width' => 0.3));

        // Logos
        $pdf->Image('logos/logo.png', 20, 17, 25);
        $pdf->Image('logos/Escudo.jpg', 165, 17, 25);

        // Encabezado
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->SetY(20);
        $pdf->Cell(0, 5, 'REPÚBLICA BOLIVARIANA DE VENEZUELA', 0, 1, 'C');
        $pdf->Cell(0, 5, 'MINISTERIO DEL PODER POPULAR PARA LA EDUCACIÓN', 0, 1, 'C');
        $pdf->Cell(0, 5, 'U.E.N ROBERTO MARTINEZ CENTENO', 0, 1, 'C');
        $pdf->Cell(0, 5, 'CARICUAO - CARACAS - DISTRITO CAPITAL', 0, 1, 'C');

        // Títuloa
        $pdf->SetY(45);
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->SetTextColor(50, 100, 150);
        $pdf->Cell(0, 8, 'CONSTANCIA DE INSCRIPCIÓN', 0, 1, 'C');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Line(($pdf->getPageWidth()-60)/2, $pdf->GetY(), ($pdf->getPageWidth()+60)/2, $pdf->GetY());
        $pdf->Ln(8);

        // Contenido
        $pdf->SetFont('helvetica', '', 11);
        $desde = date('Y', strtotime($inscripcion['desde']));  
        $hasta = date('Y', strtotime($inscripcion['hasta']));  

        $cedula_info = '';  
        if (!empty($inscripcion['tipo_cedula'])) {  
            $cedula_info = "portador(a) de la Cédula de Identidad <strong>".$inscripcion['tipo_cedula']."-".$inscripcion['cedula']."</strong>";  
        } else {  
            $cedula_info = "portador(a) de la Cédula Escolar <strong>".$inscripcion['cedula_escolar']."</strong>";  
        }  

        $ultimo_dia_mes = date('t');
        $dia_actual = date('j');
        $dias_restantes = $ultimo_dia_mes - $dia_actual;

        $html = '<div style="text-align:justify; line-height:1.5;">
            <p>Quien suscribe, <strong>LICENCIADA YAGERVI DEL CASTILLO</strong>, titular de la Cédula de Identidad <strong>V-12.798.500</strong>, en su carácter de Directora de la <strong>UNIDAD EDUCATIVA NACIONAL "ROBERTO MARTÍNEZ CENTENO"</strong>, ubicada en la parroquia Caricuao, Avenida Este 0, Caracas, Distrito Capital, adscrita a la Zona Educativa del Estado Distrito Capital, <strong>CERTIFICA</strong> que:</p>
            <p>El(la) estudiante <strong>'.mb_strtoupper($inscripcion['nombres'].' '.$inscripcion['apellidos']).'</strong>, '.$cedula_info.', nacido(a) el <strong>'.date('d/m/Y', strtotime($inscripcion['fecha_nacimiento'])).'</strong>, se encuentra formalmente inscrito(a) en este plantel para cursar el <strong>'.mb_strtoupper($inscripcion['grado']).'</strong> de Educación <strong>'.mb_strtoupper($inscripcion['nivel_id']).'</strong>, sección <strong>'.mb_strtoupper($inscripcion['id_seccion']).'</strong>, turno <strong>'.mb_strtoupper($inscripcion['turno_id']).'</strong>, correspondiente al período académico <strong>'.$desde.'-'.$hasta.'</strong>.</p>
            <p>Representante Legal: <strong>'.mb_strtoupper($inscripcion['rep_nombres'].' '.$inscripcion['rep_apellidos']).'</strong>, C.I. <strong>'.$inscripcion['rep_cedula'].'</strong>.</p>
            <p>Constancia que se expide a los '.$dias_restantes.' días del mes de '.$mes_actual.' de '.date('Y').', a solicitud de la parte interesada.</p>
            </div>';

        $pdf->writeHTML($html, true, false, true, false, '');

         // Firma centrada en la hoja
         $pdf->SetY(170);
        
         // Centrar la firma (calculamos el centro restando el ancho del texto del ancho total)
         $ancho_texto = 60; // Ancho aproximado del texto de firma
         $centro = ($pdf->getPageWidth() - $ancho_texto) / 2;
         
         $pdf->SetX($centro);
         $pdf->Line($centro, $pdf->GetY(), $centro + $ancho_texto, $pdf->GetY());
         $pdf->Ln(6);
         $pdf->SetFont('helvetica', 'B', 12);
         $pdf->SetX($centro);
         $pdf->Cell($ancho_texto, 6, 'LCDA. YAGERVI DEL CASTILLO', 0, 1, 'C');
         $pdf->SetFont('helvetica', '', 11);
         $pdf->SetX($centro);
         $pdf->Cell($ancho_texto, 6, 'Directora', 0, 1, 'C');
         $pdf->SetX($centro);
         $pdf->Cell($ancho_texto, 6, 'U.E.N ROBERTO MARTINEZ CENTENO', 0, 1, 'C');
         
         // QR en la parte inferior derecha
         $qrSize = 30;
         $qrX = $pdf->getPageWidth() - $qrSize - 20; // 20mm desde el borde derecho
         $qrY = 170; // 170mm desde arriba
         
         // Marco y código QR
         $pdf->Rect($qrX, $qrY, $qrSize, $qrSize, 'D', array('width' => 0.2));
         $pdf->Image($qrTempFile, $qrX + 1, $qrY + 1, $qrSize - 2, $qrSize - 2, 'PNG');
         
         // Texto debajo del QR
         $pdf->SetFont('helvetica', '', 8);
         $pdf->SetXY($qrX, $qrY + $qrSize);
         $pdf->Cell($qrSize, 5, 'Código de verificación', 0, 1, 'C');
         $pdf->SetXY($qrX, $qrY + $qrSize + 5);
         $pdf->SetFont('helvetica', '', 6);
         $pdf->Cell($qrSize, 5, 'Escanear para validar', 0, 1, 'C');

         // Pie de página con información de contacto
        $pdf->SetY(250);
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(0, 3, 'Av. Principal, Caricuao, Caracas - Teléfono: (0212) 123-4567 - Email: contacto@robertomartinez.edu.ve', 0, 1, 'C');
        $pdf->Cell(0, 3, 'www.robertomartinez.edu.ve - RIF: J-12345678-9', 0, 1, 'C');
        $pdf->SetTextColor(0, 0, 0);

        // Marca de agua
        $pdf->SetAlpha(0.1);
        $pdf->Image('logos/agua.jpg', 50, 80, 110, 110);
        $pdf->SetAlpha(1);

        // Eliminar archivo temporal del QR
        unlink($qrTempFile);

        $pdf->Output('constancia_'.$inscripcion['cedula'].'.pdf', 'I');
    }
} else {  
    echo "<div style='text-align:center; padding:20px;'>No se encontraron datos de inscripción</div>";  
}  
ob_end_flush();
?>