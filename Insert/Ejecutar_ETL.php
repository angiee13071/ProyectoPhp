<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importación de Datos</title>
    <link rel="stylesheet" href="../styles.css">
    <!-- Agregar referencia a la fuente Montserrat -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap" rel="stylesheet">
</head>

<body>
    <div class="header-container">
        <header>
            <nav>
                <ul class="nav-list">
                    <li class="nav-item"><a href="#" style="text-decoration: none;">Inicio ▼</a></li>
                    <li class="nav-item"><a href="#" style="text-decoration: none;">Acerca de ▼</a></li>
                    <li class="nav-item"><a href="#" style="text-decoration: none;">Contacto ▼</a></li>
                </ul>
            </nav>
            <img src="Assets/images/uploads/logo_ud.png" alt="Logo" class="logo">
        </header>
    </div>
    <div class="ETL">
        <div>
            <h1>Importación de Datos</h1>
            <button class="button_ETL" style=" " onclick="goBack()">&#8592;</button>
        </div>

        <?php
        
$url1 = "file:///C:/xampp/htdocs/ProyectoPhp/Insert/Listado_de_admitidos.csv";
$url2 = "file:///C:/xampp/htdocs/ProyectoPhp/Insert/Lista_de_Egresados_por_Proyecto.csv";
$url3 = "file:///C:/xampp/htdocs/ProyectoPhp/Insert/Listado_matriculados_período_actual.csv";
$url4 = "file:///C:/xampp/htdocs/ProyectoPhp/Insert/Listado_matrículados_a_primer_semestre.csv";

// paso 0 :Validar si existe BD
require_once '../ConexionBD.php';

// validar si existen los archivos
if (file_exists($url1) && file_exists($url2) && file_exists($url3) && file_exists($url4)) {
echo' <div style="background-color: #efffef; color: black; padding: 10px; text-align: center;border: 2px solid #4CAF50;width: 63rem;
 height: 10rem; transform: skew(150deg);margin-bottom: 0rem;">
     <div style="font-size: 3rem; color: #4CAF50;text-align: initial;">❶</div>
     <span style="font-size: 2rem;color:#4CAF50">✔ EXTRACCIÓN Y TRANSFORMACIÓN</span><br>
     <div>Extracción y transformación exitosa de los datos.</div>
     <div style="position: absolute;  left: 50%;">
   
    </div>
 </div>';
 echo' <span style="font-size: 4rem;margin-bottom: 0.5rem;">&#8595;</span>';
} else{
    echo' <div style="background-color: #efffef; color: black; padding: 10px; text-align: center;border: 2px solid rgba(255, 99, 132, 1);width: 63rem;
    height: 10rem; transform: skew(150deg);margin-bottom: 0rem;">
        <div style="font-size: 3rem; color: rgba(255, 99, 132, 1);text-align: initial;">❶</div>
        <span style="font-size: 2rem;color:rgba(255, 99, 132, 1)">X EXTRACCIÓN Y TRANSFORMACIÓN</span><br>
        <div>No se encuentran todos los archivos en su totalidad, por favor validar.</div>
        <div style="position: absolute;  left: 50%;">
      
       </div>
    </div>';
    echo' <span style="font-size: 4rem;margin-bottom: 0.5rem;">&#8595;</span>';
}

// Tomar el archivo de egresados para subir los programas academicos y periodos:
require 'Insert_EgresadoCSV_Programa_Periodo.php';

 //Tomar el archivo egresado para subirlo a la tabla estudiantes:
require 'Insert_EgresadoCSV_Estudiante.php';

//Tomar el archivo de matriculados actualmente para subirlos a la tabla estudiante:
require 'Insert_MatriculadosCSV_Estudiante.php';
    
// Tomar el archivo de matriculados actualmente para subirlos a la tabla matriculados:
require 'Insert_MatriculadosCSV_Matriculado.php';

// Tomar el archivo de primiparos y llenar la tabla estudiante
require 'Insert_PrimiparosCSV_Estudiante.php';

// Tomar el archivo de primiparos y llenar la tabla primiparo:
require 'Insert_PrimiparosCSV_Primiparo.php';

// Llamar y ejecutar el archivo EgresadoGraduadoCSV.php
 require 'Insert_EgresadoCSV_Graduado.php';
 //Tomar el archivo admitidos para subirlo a la tabla estudiantes:
 require 'Insert_AdmitidosCSV_Estudiante.php';
 //Tomar archivo admitidos y llenar talbla admitido
 require 'Insert_AdmitidosCVS_Admitido.php';
// Llamar y ejecutar procedimiento almacenado para llenar tablas total y retirado
require 'CalcularTotal.php';


echo '<div style="background-color: #efffef; color: black; padding: 10px; text-align: center;border-radius: 50rem;
border: 2px solid #4CAF50; width: 70rem; position: relative;margin-bottom: 2rem;">
<span style="font-size: 2rem;color:#4CAF50">✔ FIN</span><br>
Proceso de importación completado
<div style="position: absolute; top: 1rem; left: 1rem; font-size: 3rem;color:#4CAF50">⓫</div>
<div style="position: absolute;  left: 50%;">
</div>
</div>';
?>
        <!-- <h2>Proceso de importación completado</h2> -->
        <!-- <p>Todos los archivos CSV se han procesado exitosamente.</p>
        <p>Los datos se han insertado en las respectivas tablas en la base de datos.</p> -->
    </div>
    <script>
    function goBack() {
        window.history.back();
    }
    </script>
</body>

</html>