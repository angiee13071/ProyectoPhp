<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>

<body>
    <?php include 'header.html'; ?>
    <div class="card">
        <div class="title">
            <h1>Módulos:</h1>
        </div>
        <a class="buttonETL" href="../Controlador/Ejecutar.php">ETL <i class="fas fa-exchange-alt"></i>
        </a>
        <a class="buttonBD" href="../Vista/TablasBD.php">Base de datos <i class="fas fa-database"></i></a>
        <a class="button" href="../Vista/View_Desercion.php">Deserción estudiantil <i class="fas fa-user-times"
                style="color: white;"></i></a>
        <a class="button1" href="../Vista/View_Permanencia.php">Permanencia estudiantil <i class="fas fa-user-check"
                style="color: white;"></i>
        </a>
        <a class="buttonData" href="../Vista/View_Data_General.php">Estadísticas Estudiantiles <i
                class="fas fa-chart-bar"></i></a>
    </div>
</body>

</html>