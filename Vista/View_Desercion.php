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
        <h1>Deserción estudiantil:</h1>
    </div>
    <a class="button" href="http://localhost/ProyectoPhp/Vista/View_Desercion_Cohorte.php">Deserción por cohorte</a>
    <a class="button" href="http://localhost/ProyectoPhp/Vista/View_Desercion_Semestre.php">Deserción por semestre</a>
    <a class="button" href="http://localhost/ProyectoPhp/Vista/View_Desercion_Anio.php">Deserción por año</a>
    <a class="button" href="http://localhost/ProyectoPhp/Vista/View_Desercion_Promedio.php">Promedio acumulado</a>
    <button class="arrow-button" onclick="goBack()">&#8592;</button>

</div>
<script src="index.js"></script>
<script>
function goBack() {
    window.history.back();
}
</script>

</html>