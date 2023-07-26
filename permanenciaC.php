<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permanencia por Cohorte</title>
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
            <h1>Permanencia por Cohorte</h1>
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
    // Obtener los datos de la tabla 'graduado' y 'estudiante'
    <?php
    include "conexion.php"; // Incluye el archivo de conexión a la base de datos

    $cohortes = [];
    $permanencias = [];

    $query = "SELECT
                periodo.cohorte,
                COUNT(DISTINCT graduado.id_estudiante) AS graduados,
                COUNT(DISTINCT estudiante.id_estudiante) AS estudiantes
              FROM
                graduado
              INNER JOIN periodo ON graduado.id_periodo = periodo.id_periodo
              INNER JOIN estudiante ON graduado.id_estudiante = estudiante.id_estudiante
              GROUP BY
                periodo.cohorte";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $cohortes[] = $row['cohorte'];
        $permanencias[] = ($row['graduados'] / $row['estudiantes']) * 100;
      }
    }

    $conn->close();
    ?>

    // Crear la gráfica inicial
    var ctx = document.getElementById('permanencia-chart').getContext('2d');
    var chartTypeSelect = document.getElementById('chart-type');
    var chart;

    // Función para crear el gráfico
    function createChart(chartType) {
        if (chart) {
            chart.destroy(); // Destruir el gráfico existente si ya se ha creado
        }

        var data = {
            labels: <?php echo json_encode($cohortes); ?>,
            datasets: [{
                label: 'Permanencia por Cohorte',
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
    </script>
    <script>
    function goBack() {
        window.history.back();
    }
    </script>
</body>

</html>