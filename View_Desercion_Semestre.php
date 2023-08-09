<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deserción por semestre</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <?php include 'header.html'; ?>
    <div class="card">
        <div class="title">
            <h1>Deserción por semestre:</h1>
        </div>
        <div class="chart-container">
            <canvas id="desercion-chart"></canvas>
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
    // Obtener los datos de la tabla 'total'
    <?php
    include "ConexionBD.php"; // Incluye el archivo de conexión a la base de datos

    $semestres = [];
    $desertores = [];
    $primiparos = [];
    $tasa_desercion = [];

    $query = "SELECT 
    CONCAT(periodo.anio, '-', periodo.semestre) AS semestre,
    CASE
        WHEN (
            (SELECT COUNT(*) FROM primiparo WHERE id_periodo = periodo.id_periodo) -
            (SELECT COUNT(*) FROM graduado WHERE id_periodo = periodo.id_periodo) -
            (SELECT COUNT(*) FROM retirado WHERE id_periodo = periodo.id_periodo)
        ) < 0 THEN 0
        ELSE (
            (SELECT COUNT(*) FROM primiparo WHERE id_periodo = periodo.id_periodo) -
            (SELECT COUNT(*) FROM graduado WHERE id_periodo = periodo.id_periodo) -
            (SELECT COUNT(*) FROM retirado WHERE id_periodo = periodo.id_periodo)
        )
    END AS desertores,
    (SELECT COUNT(*) FROM primiparo WHERE id_periodo = periodo.id_periodo) AS primiparos,
    ROUND(
        CASE
            WHEN (
                (SELECT COUNT(*) FROM primiparo WHERE id_periodo = periodo.id_periodo) -
                (SELECT COUNT(*) FROM graduado WHERE id_periodo = periodo.id_periodo) -
                (SELECT COUNT(*) FROM retirado WHERE id_periodo = periodo.id_periodo)
            ) < 0 THEN 0
            ELSE (
                (SELECT COUNT(*) FROM retirado WHERE id_periodo = periodo.id_periodo) / (SELECT COUNT(*) FROM primiparo WHERE id_periodo = periodo.id_periodo)
            ) * 100
        END
    , 2) AS tasa_desercion
FROM periodo
ORDER BY semestre;";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $semestres[] = $row['semestre'];
        $desertores[] = $row['desertores'];
        $primiparos[] = $row['primiparos'];
        $tasa_desercion[]=$row['tasa_desercion'];
      }
    }

    $conn->close();
    ?>

    // Crear la gráfica inicial
    var ctx = document.getElementById('desercion-chart').getContext('2d');
    var chartTypeSelect = document.getElementById('chart-type');
    var chart;

    // Función para crear el gráfico
    function createChart(chartType) {
        if (chart) {
            chart.destroy(); // Destruir el gráfico existente si ya se ha creado
        }

        var data = {
            labels: <?php echo json_encode($semestres); ?>,
            datasets: [{
                label: 'Desertores',
                data: <?php echo json_encode($desertores); ?>,
                backgroundColor: 'rgba(255, 159, 49, 0.87)',
                borderColor: 'rgba(255, 159, 49, 0.87)',
                borderWidth: 1
            }, {
                label: 'Primíparos',
                data: <?php echo json_encode($primiparos); ?>,
                backgroundColor: 'rgba(0, 255, 0, 0.58)',
                borderColor: 'rgba(0, 255, 0, 0.58)',
                borderWidth: 1
            }, {
                label: 'Tasa de deserción',
                data: <?php echo json_encode($tasa_desercion); ?>,
                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                borderColor: 'rgba(255, 99, 132, 1)',
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