<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <?php include 'header.html'; ?>
    <div class="card">
        <div class="title">
            <h1>Módulos:</h1>
        </div>
        <a class="buttonETL" href="../Controlador/Ejecutar.php">ETL</a>
        <a class="buttonBD" href="../Vista/TablasBD.php">Base de datos</a>
        <a class="button" href="../Vista/View_Desercion.php">Deserción estudiantil</a>
        <a class="button1" href="../Vista/View_Permanencia.php">Permanencia estudiantil</a>
        <a class="buttonData" href="../Vista/View_Data_General.php">Estadísticas Estudiantiles</a>
    </div>
</body>

</html>