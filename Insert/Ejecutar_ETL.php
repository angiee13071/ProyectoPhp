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
                    <li class="nav-item"><a href="https://www.udistrital.edu.co/directorio"
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

// Tomar el archivo de egresados para subir los programas academicos y periodos:
require 'Insert_EgresadoCSV_Programa_Periodo.php';

 //Tomar el archivo egresado para subirlo a la tabla estudiantes:
//require 'Insert_EgresadoCSV_Estudiante.php';

//Tomar el archivo de matriculados actualmente para subirlos a la tabla estudiante:
//require 'Insert_MatriculadosCSV_Estudiante.php';
    
// Tomar el archivo de matriculados actualmente para subirlos a la tabla matriculados:
//require 'Insert_MatriculadosCSV_Matriculado.php';

// Tomar el archivo de primiparos y llenar la tabla estudiante
//require 'Insert_PrimiparosCSV_Estudiante.php';

//Tomar el archivo de primiparos y llenar la tabla primiparo:
//require 'Insert_PrimiparosCSV_Primiparo.php';

// Llamar y ejecutar el archivo EgresadoGraduadoCSV.php
//require 'Insert_EgresadoCSV_Graduado.php';

//Tomar el archivo admitidos para subirlo a la tabla estudiantes:
//require 'Insert_AdmitidosCSV_Estudiante.php';
 
//Tomar archivo admitidos y llenar talbla admitido
//require 'Insert_AdmitidosCVS_Admitido.php';

// Llamar y ejecutar procedimiento almacenado para llenar tablas total y retirado
require 'CalcularTotal.php';

?>
    </div>
    <script>
    function goBack() {
        window.history.back();
    }
    </script>
</body>

</html>