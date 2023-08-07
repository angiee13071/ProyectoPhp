<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="styles.css">
</head>

<?php include 'header.html'; ?>
<div class="card">
    <div class="title">
        <h1>Permanencia estudiantil:</h1>
    </div>
    <a class="button1" href="http://localhost/ProyectoPhp/View_Permanencia_Cohorte.php">Permanencia por cohorte</a>
    <a class="button1" href="http://localhost/ProyectoPhp/View_Permanencia_Semestre.php">Permanencia por Semestre</a>
    <a class="button1" href="http://localhost/ProyectoPhp/View_Permanencia_Anio.php">Permanencia por Año</a>
    <a class="button1" href="http://localhost/ProyectoPhp/View_Permanencia_Promedio.php">Promedio acumulado</a>
    <a class="button1" href="http://localhost/ProyectoPhp/View_Permanencia_General.php">Datos generales</a>
    <button class="arrow-button" onclick="goBack()">&#8592;</button>

</div>
<script src="index.js"></script>

</html>