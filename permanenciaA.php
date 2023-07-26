<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permanencia por Año</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
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

<body>
    <div class="card">
        <div class="title">
            <h1>Permanencia por Año</h1>
        </div>
        <div class="chart-container">
            <canvas id="permanencia-chart"></canvas>
        </div>
        <select id="chart-type">
            <option value="line">Gráfico de líneas</option>
            <option value="bar">Gráfico de columnas</option>
        </select>
        <button class="arrow-button" onclick="goBack()">&#8592;</button>
    </div>

    <script>
    // Obtener los datos de la tabla 'matriculado' y 'retirado'
    <?php
    include "conexion.php"; // Incluye el archivo de conexión a la base de datos

    $anios = [];
    $permanencias = [];

    $query = "SELECT
                YEAR(p.fecha_inicio) AS anio,
                COUNT(DISTINCT m.id_estudiante) AS matriculados,
                COUNT(DISTINCT r.id_estudiante) AS retirados
              FROM
                periodo p
              LEFT JOIN matriculado m ON p.id_periodo = m.id_periodo
              LEFT JOIN retirado r ON p.id_periodo = r.id_periodo
              GROUP BY
                YEAR(p.fecha_inicio)";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $anios[] = $row['anio'];
        $matriculados = ($row['matriculados'] != null) ? $row['matriculados'] : 0;
        $retirados = ($row['retirados'] != null) ? $row['retirados'] : 0;
        $permanencias[] = ($matriculados - $retirados) * 100 / $matriculados;
      }
    } else {
      echo "No se puede calcular la permanencia debido a que no hay estudiantes retirados.";
    }

    $conn->close();
    ?>

    // Crear la gráfica inicial si hay datos disponibles
    if (<?php echo count($anios); ?> > 0) {
        var ctx = document.getElementById('permanencia-chart').getContext('2d');
        var chartTypeSelect = document.getElementById('chart-type');
        var chart;

        // Función para crear el gráfico
        function createChart(chartType) {
            if (chart) {
                chart.destroy(); // Destruir el gráfico existente si ya se ha creado
            }

            var data = {
                labels: <?php echo json_encode($anios); ?>,
                datasets: [{
                    label: 'Permanencia por Año',
                    data: <?php echo json_encode($permanencias); ?>,
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
    }
    </script>
    <script>
    function goBack() {
        window.history.back();
    }
    </script>
</body>

</html>