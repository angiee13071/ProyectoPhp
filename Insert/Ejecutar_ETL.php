<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importación de Datos</title>
    <link rel="stylesheet" href="styles.css">
    <!-- Agregar referencia a la fuente Montserrat -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap" rel="stylesheet">
</head>


<body>
    <div style="font-family: system-ui;font-size: 1.5rem;">
        <header style="background: linear-gradient(95deg, #FEFBFB, #FFBF58, #FF8F46, #FCD96C);">
            <nav style="color: black;">

                <ul
                    style="    display: flex; flex-direction: row;padding: 1.4rem;align-items: center;list-style: none;color: black;">
                    <img src="../Assets/images/uploads/logo_ud.png" alt="Logo" style="width: 15rem;">
                    <li style="width: 6rem;color: black;margin-left: 2rem;"> <a href="#"
                            style="text-decoration: none;">Inicio ▼</a></li>
                    <li style="width: 9rem;color: black;margin-left: 2rem;"><a href="#"
                            style="text-decoration: none;">Acerca de ▼</a>
                    </li>
                    <li style="width: 9rem;color: black;margin-left: 2rem;"><a href="#"
                            style="text-decoration: none;">Contacto ▼</a></li>
                </ul>
            </nav>
        </header>
    </div>
    <div style="font-family: system-ui;margin-left: 2rem;font-size:1.6rem;line-height: 2.5rem;">
        <div style="display: flex; flex-direction: row;">
            <h1>Importación de Datos</h1>
            <button style=" position: absolute;
  /* bottom: 20px;
  right: 20px; */
  width: 50px;
  height: 50px;
  background-color: rgb(5, 5, 5);
  color: white;
  border: none;
  border-radius: 50%;
  font-size: 24px;
  line-height: 50px;
  cursor: pointer;
  top: 3.5rem;
  right: 3rem;" onclick="goBack()">&#8592;</button>
        </div>

        <?php
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

// Llamar y ejecutar procedimiento almacenado para llenar tablas total y retirado
require 'CalcularTotal.php';

?>
        <h2>Proceso de importación completado</h2>
        <p>Todos los archivos CSV se han procesado exitosamente.</p>
        <p>Los datos se han insertado en las respectivas tablas en la base de datos.</p>
    </div>
    <script>
    function goBack() {
        window.history.back();
    }
    </script>
</body>

</html>