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
            <h1>Promedio Acumulado de Graduados:</h1>
        </div>
        <div class="chart-container">
            <canvas id="promedio-chart"></canvas>
        </div>
        <select id="chart-type">
            <option value="line">Gráfico de líneas</option>
            <option value="bar">Gráfico de barras</option>
        </select>
        <button class="arrow-button" onclick="goBack()">&#8592;</button>
    </div>

    <script>
    // Obtener los datos de la consulta SQL
    <?php
    include "conexion.php"; // Incluye el archivo de conexión a la base de datos

    $anios = [];
    $semestres = [];
    $promedios = [];

    $query = "SELECT
                periodo.anio,
                periodo.semestre,
                (
                  SELECT AVG(g.promedio)
                  FROM graduado g
                  INNER JOIN periodo p ON g.id_periodo = p.id_periodo
                  WHERE p.id_periodo <= periodo.id_periodo
                ) AS promedio_acumulado
              FROM
                periodo
              ORDER BY
                periodo.anio, periodo.semestre";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $anios[] = $row['anio'];
        $semestres[] = $row['semestre'];
        $promedios[] = $row['promedio_acumulado'];
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