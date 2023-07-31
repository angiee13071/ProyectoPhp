<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permanencia por Semestre</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<div class="header" style="font-family: system-ui;font-size: 1.5rem;">
    <header style="background: linear-gradient(95deg, #FEFBFB, #FFBF58, #FF8F46, #FCD96C);">
        <nav style="color: black;">

            <ul
                style="display: flex; flex-direction: row;padding: 1.4rem;align-items: center;list-style: none;color: black;">
                <img src="Assets/images/uploads/logo_ud.png" alt="Logo" style="width: 15rem;">
                <li style="width: 6rem;color: black;margin-left: 2rem;"> <a href="#"
                        style="text-decoration: none;">Inicio
                        ▼</a></li>
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
            <h1>Permanencia por Semestre</h1>
        </div>
        <div class="chart-container">
            <canvas id="permanencia-chart"></canvas>
        </div>
        <div>
            <select id="chart-carrer">
                <option value="all">Todas las carreras</option>
                <option value="tec">Tecnología en sistematización de datos</option>
                <option value="ing">Ingeniería telemática</option>
            </select>
        </div>
        <div>
            <select id="chart-type">
                <option value="line">Gráfico de líneas</option>
                <option value="bar">Gráfico de columnas</option>
            </select>
        </div>
        <button class="arrow-button" onclick="goBack()">&#8592;</button>
    </div>

    <script>
    // Obtener los datos de la tabla 'graduado' y 'estudiante'
    <?php
        include "conexion.php"; // Incluye el archivo de conexión a la base de datos

        $cohortes = [];
        $permanencias = [];

        // Función para obtener los datos dependiendo de la carrera seleccionada
        function fetchData($carrera) {
            global $conn, $cohortes, $permanencias;

            $query = "SELECT
            CONCAT(p.anio, '-', p.semestre) AS periodo_actual,
            CONCAT(p.anio, '-', (p.semestre - 1)) AS periodo_anterior,
            p.id_periodo,
            p.cohorte,
            COUNT(DISTINCT m.id_estudiante) AS matriculado,
            FORMAT((COUNT(DISTINCT m.id_estudiante) / LAG(COUNT(DISTINCT m.id_estudiante)) OVER (ORDER BY p.anio, p.semestre)) * 100, 2) AS permanencia,
            e.carrera 
            FROM
            periodo p
            LEFT JOIN matriculado m ON p.id_periodo = m.id_periodo
            LEFT JOIN estudiante e ON m.id_estudiante = e.id_estudiante 
            WHERE
            m.estado_matricula = 'ESTUDIANTE MATRICULADO'";

            if ($carrera === 'all') {
              
            }elseif($carrera === 'tec'){
                $query .= " AND e.carrera = 'TECNOLOGIA EN SISTEMATIZACION DE DATOS (CICLOS PROPEDEUTICOS)'";
            }elseif($carrera === 'ing'){
                $query .= " AND e.carrera = 'INGENIERIA EN TELEMATICA (CICLOS PROPEDEUTICOS)'";
            }

            $query .= " GROUP BY
            periodo_actual, periodo_anterior, p.id_periodo, p.cohorte, e.carrera 
            ORDER BY
            p.anio, p.semestre;";

            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $cohortes[] = $row['periodo_actual'];
                    $permanencias[] = floatval($row['permanencia']);
                }
            }
        }

        // Obtener datos por defecto (Todas las carreras)
        fetchData('all');
        ?>

    // Crear la gráfica inicial
    var ctx = document.getElementById('permanencia-chart').getContext('2d');
    var chartTypeSelect = document.getElementById('chart-type');
    var chartSelectCarrer = document.getElementById('chart-carrer');
    var chart;

    // Función para filtrar los datos por carrera y actualizar el gráfico
    function updateChart() {
        var selectedCarrera = chartSelectCarrer.value;

        // Obtener los datos correspondientes a la carrera seleccionada
        fetchData(selectedCarrera);

        // Actualizar la gráfica con los datos filtrados
        createChart(chartTypeSelect.value, <?php echo json_encode($cohortes); ?>,
            <?php echo json_encode($permanencias); ?>);
    }

    // Función para crear el gráfico
    function createChart(chartType, cohortesData, permanenciasData) {
        if (chart) {
            chart.destroy(); // Destruir el gráfico existente si ya se ha creado
        }

        var data = {
            labels: cohortesData,
            datasets: [{
                label: 'Permanencia por Semestre',
                data: permanenciasData,
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
        createChart(selectedType, <?php echo json_encode($cohortes); ?>,
            <?php echo json_encode($permanencias); ?>);
    });

    // Evento de cambio de carrera
    chartSelectCarrer.addEventListener('change', function() {
        updateChart();
    });

    // Crear el gráfico inicial
    createChart(chartTypeSelect.value, <?php echo json_encode($cohortes); ?>,
        <?php echo json_encode($permanencias); ?>);
    </script>
    <script>
    function goBack() {
        window.history.back();
    }
    </script>
</body>

</html>