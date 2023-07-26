<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="styles.css">
</head>
<?php
  
  ?>
<div class="header" style="font-family: system-ui;font-size: 1.5rem;">
    <header style="background: linear-gradient(95deg, #FEFBFB, #FFBF58, #FF8F46, #FCD96C);">
        <nav style="color: black;">

            <ul
                style="    display: flex; flex-direction: row;padding: 1.4rem;align-items: center;list-style: none;color: black;">
                <img src="Assets/images/uploads/logo_ud.png" alt="Logo" style="width: 15rem;">
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
<div class="card">
    <div class="title">
        <h1>Permanencia estudiantil:</h1>
    </div>
    <a class="button1" href="http://localhost/ProyectoPhp/permanenciaC.php">Permanencia por cohorte</a>
    <a class="button1" href="http://localhost/ProyectoPhp/permanenciaS.php">Permanencia por Semestre</a>
    <a class="button1" href="http://localhost/ProyectoPhp/permanenciaA.php">Permanencia por Año</a>
    <a class="button1" href="http://localhost/ProyectoPhp/promedioP.php">Promedio acumulado</a>
    <a class="button1" href="http://localhost/ProyectoPhp/DatosGeneralesP.php">Datos generales</a>
    <button class="arrow-button" onclick="goBack()">&#8592;</button>

</div>
<script src="index.js"></script>

</html>