<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promedio Ponderado en Permanencia Estudiantil</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
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
            <h1>Promedio Ponderado en Permanencia Estudiantil</h1>
        </div>
        <div class="chart-container">
            <canvas id="promedio-chart"></canvas>
        </div>
        <select id="chart-type">
            <option value="bar">Gráfico de barras</option>
            <option value="line">Gráfico de líneas</option>
        </select>
        <button class="arrow-button" onclick="goBack()">&#8592;</button>
    </div>

    <script>
    // Obtener los datos para el promedio ponderado en permanencia estudiantil
    <?php
    $desertores = 215;
    $matriculadosPeriodoAnterior = 1244;
    $matriculadosPeriodoActual = 107;
    $graduados = 107;
    $matriculadosPeriodoGraduacion = 1484;
    $pesoRetencion = 0.70;
    $pesoGraduacion = 0.30;

    $tasaRetencion = ($matriculadosPeriodoAnterior - $desertores) / $matriculadosPeriodoAnterior * 100;
    $tasaGraduacion = $graduados / $matriculadosPeriodoGraduacion * 100;

    $promedioPonderado = ($tasaRetencion * $pesoRetencion) + ($tasaGraduacion * $pesoGraduacion);
    ?>

    // Crear la gráfica del promedio ponderado
    var ctx = document.getElementById('promedio-chart').getContext('2d');
    var chartTypeSelect = document.getElementById('chart-type');
    var chart;

    // Función para crear el gráfico
    function createChart(chartType) {
        if (chart) {
            chart.destroy(); // Destruir el gráfico existente si ya se ha creado
        }

        var data = {
            labels: ['Promedio Ponderado'],
            datasets: [{
                label: 'Promedio Ponderado',
                data: [<?php echo $promedioPonderado; ?>],
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        };

        var options = {
            responsive: true,
            maintainAspectRatio: false
        };

        chart = new Chart(ctx, {
            type: chartType,
            data: data,
            options: options
        });
    }

    // Evento de cambio de tipo de gráfico
    chartTypeSelect.addEventListener('change', function() {
        var selectedType = chartTypeSelect.value;
        createChart(selectedType);
    });

    // Crear el gráfico inicial
    createChart(chartTypeSelect.value);
    </script>
    <script>
    function goBack() {
        window.history.back();
    }
    </script>
</body>

</html>