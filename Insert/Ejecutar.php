<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importación de Datos</title>
</head>

<body>
    <h1>Importación de Datos</h1>

    <?php
    // Llamar y ejecutar el archivo EgresadoProgramaCSV.php
    require 'EgresadoProgramaCSV.php';

    // Llamar y ejecutar el archivo EgresadoEstudianteCSV.php
    require 'EgresadoEstudianteCSV.php';

    // Llamar y ejecutar el archivo PeriodoActualEstudianteCSV.php
    require 'PeriodoActualEstudianteCSV.php';

    // Llamar y ejecutar el archivo MatriculadoEstudianteCSV.php
     require 'MatriculadoEstudianteCSV.php';

    // Llamar y ejecutar el archivo MatriculadoPrimerSemestreCSV.php
    require 'MatriculadoPrimerSemestreCSV.php';

    // Llamar y ejecutar el archivo EgresadoGraduadoCSV.php
    require 'EgresadoGraduadoCSV.php';

    // Llamar y ejecutar el archivo PeriodoActualMatriculadoCSV.php
     require 'PeriodoActualMatriculadoCSV.php';

    // Llamar y ejecutar procedimiento almacenado para llenar tablas total y retirado
    require 'total.php';
    ?>
    <h2>Proceso de importación completado</h2>
    <p>Todos los archivos CSV se han procesado exitosamente.</p>
    <p>Los datos se han insertado en las respectivas tablas en la base de datos.</p>
</body>

</html>