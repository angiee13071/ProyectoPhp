<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promedio Acumulado</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<?php include 'header.html'; ?>

<body>
    <div class="card">
        <div class="title">
            <h1>Promedio Acumulado de Graduados:</h1>
        </div>
        <div class="chart-container">
            <canvas id="promedio-chart"></canvas>
        </div>
        <div>
            <select id="chart-type">
                <option value="line">Gráfico de líneas</option>
                <option value="bar">Gráfico de columnas</option>
            </select>
        </div>
        <div>
            <select id="chart-carrera">
                <option value="all">Todas las carreras</option>
                <option value="tec">Tecnología en sistematización de datos</option>
                <option value="ing">Ingeniería telemática</option>
            </select>
        </div>
        <button class="arrow-button" onclick="goBack()">&#8592;</button>
    </div>

    <script>
    // Obtener los datos de la consulta SQL
    <?php
    include "ConexionBD.php"; // Incluye el archivo de conexión a la base de datos

    $anios = [];
    $semestres = [];
    $promedios = [];

    $query = "SELECT
    t1.anio,
    t1.semestre,
    CASE
        WHEN t2.primiparos > 0 THEN
            ROUND((t2.retirados / t2.primiparos) * 100, 2)
        ELSE
            0
    END AS tasa_desercion
FROM
    periodo t1
LEFT JOIN (
    SELECT
        p.id_periodo,
        COUNT(DISTINCT pr.id_primiparo) AS primiparos,
        COUNT(DISTINCT r.id_retiro) AS retirados
    FROM
        periodo p
    LEFT JOIN primiparo pr ON p.id_periodo = pr.id_periodo
    LEFT JOIN retirado r ON p.id_periodo = r.id_periodo
    GROUP BY
        p.id_periodo
) t2 ON t1.id_periodo = t2.id_periodo
ORDER BY
    t1.anio, t1.semestre
LIMIT 0, 1000;";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $anios[] = $row['anio'];
        $semestres[] = $row['semestre'];
        $promedios[] = $row['tasa_desercion'];
      }
    }

    $conn->close();
    ?>

    // Crear la gráfica inicial
    var ctx = document.getElementById('promedio-chart').getContext('2d');
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
                label: 'Promedio Acumulado',
                data: <?php echo json_encode($promedios); ?>,
                backgroundColor: chartType === 'bar' ? 'rgba(54, 162, 235, 0.5)' :
                    'rgba(75, 192, 192, 0.5)',
                borderColor: chartType === 'bar' ? 'rgba(54, 162, 235, 1)' : 'rgba(75, 192, 192, 1)',
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
</body>

</html>

<script>
function goBack() {
    window.history.back();
}
</script>