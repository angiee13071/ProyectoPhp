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

                    <li class="nav-item"><a href="https://www.udistrital.edu.co/inicio"
                            style="text-decoration: none;">Inicio ▼</a></li>
                    <li class="nav-item"><a href="https://ftecnologica.udistrital.edu.co/"
                            style="text-decoration: none;">Contacto ▼</a></li>
                    <li class="nav-item"><a
                            href="https://www.udistrital.edu.co/admisiones/index.php/oferta/programas/ingenieria-en-telematica-por-ciclos-propedeuticos"
                            style="text-decoration: none;">Ingeniería ▼</a></li>
                    <li class="nav-item"><a
                            href="https://www.udistrital.edu.co/admisiones/index.php/oferta/programas/tecnologia-en-sistematizacion-de-datos-por-ciclos-propedeuticos"
                            style="text-decoration: none;">Tecnología▼</a></li>

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
        // error_reporting(E_ALL);
        // ini_set('display_errors', 1);

// paso 0 :Validar si existe BD




//#1 Tomar el archivo de egresados para subir los programas academicos y periodos:
require 'Insert_EgresadoCSV_Programa_Periodo.php';

 //#2 Tomar el archivo egresado para subirlo a la tabla estudiantes:
require 'Insert_EgresadoCSV_Estudiante.php';

//#3 Tomar el archivo de matriculados actualmente para subirlos a la tabla estudiante:
require 'Insert_MatriculadosCSV_Estudiante.php';
    
//#4 Tomar el archivo de matriculados actualmente para subirlos a la tabla matriculados:
require 'Insert_MatriculadosCSV_Matriculado.php';

//#5 Tomar el archivo de primiparos y llenar la tabla estudiante
require 'Insert_PrimiparosCSV_Estudiante.php';

//#6 Tomar el archivo de primiparos y llenar la tabla primiparo:
require 'Insert_PrimiparosCSV_Primiparo.php';

//#7  Llamar y ejecutar el archivo EgresadoGraduadoCSV.php
 require 'Insert_EgresadoCSV_Graduado.php';
 //#8 Tomar el archivo admitidos para subirlo a la tabla estudiantes:
 require 'Insert_AdmitidosCSV_Estudiante.php';
 //#9 Tomar archivo admitidos y llenar talbla admitido
 require 'Insert_AdmitidosCVS_Admitido.php';
//#10 Llamar y ejecutar procedimiento almacenado para llenar tablas total y retirado
require 'CalcularTotal.php';
//#fin

echo '<div style="background-color: #efffef; color: black; padding: 10px; text-align: center;border-radius: 50rem;
border: 2px solid #4CAF50; width: 70rem; position: relative;margin-bottom: 2rem;">
<span style="font-size: 2rem;color:#4CAF50">✔ FIN</span><br>
Proceso de importación completado
<div style="position: absolute; top: 1rem; left: 1rem; font-size: 3.6rem;color:#4CAF50">⓬</div>
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