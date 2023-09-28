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
<?php include 'header.html'; ?>

<body>
    <div class="card">
        <div class="title">
            <h1>Permanencia por Cohorte</h1>
        </div>
        <div class="chart-container">
            <canvas id="permanencia-chart"></canvas>
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
    // Obtener los datos de la tabla 'graduado' y 'estudiante'
    <?php
        include "ConexionBD.php"; // Incluye el archivo de conexión a la base de datos
        // Crear una instancia de la clase DatabaseConnection
$dbConnection = new DatabaseConnection();
$conn = $dbConnection->getDBConnection();
        //grafica 1
        $cohortes = [];
        $permanencias = [];
        //grafica 2
        $cohortesTec = [];
        $permanenciasTec = [];
        //grafica 3
        $cohortesIng = [];
        $permanenciasIng = [];

        // Función para obtener los datos dependiendo de la carrera seleccionada
        function fetchData($carrera) {
            global $conn, $cohortes, $permanencias,$cohortesTec,$permanenciasTec,$cohortesIng,$permanenciasIng;

            $query = "SELECT
            CONCAT(p_actual.anio, '-', p_actual.cohorte) AS periodo_actual,
            CONCAT(p_actual.anio, '-', p_actual.cohorte) AS cohorte,
            CONCAT(p_anterior.anio, '-', p_anterior.cohorte) AS periodo_anterior,
            SUM(t_actual.matriculados) AS matriculados_actual,
            LAG(SUM(t_actual.matriculados)) OVER (ORDER BY p_actual.id_periodo) AS matriculados_anterior,
            CASE
                WHEN (LAG(SUM(t_actual.matriculados)) OVER (ORDER BY p_actual.id_periodo) / SUM(t_actual.matriculados)) * 100 > 100
                THEN 100
                ELSE (LAG(SUM(t_actual.matriculados)) OVER (ORDER BY p_actual.id_periodo) / SUM(t_actual.matriculados)) * 100
            END AS permanencia
        FROM
            total t_actual  
        JOIN
            periodo p_actual ON t_actual.id_periodo = p_actual.id_periodo
        LEFT JOIN
            periodo p_anterior ON p_actual.id_periodo = p_anterior.id_periodo + 1
         -- where t_actual.id_programa='578'
         ";

            if ($carrera === 'all') {
              
            }elseif($carrera === 'tec'){
                $query .= " where t_actual.id_programa='578'";
            }elseif($carrera === 'ing'){
                $query .= " where t_actual.id_programa='678'";
            }

            $query .= " GROUP BY
            p_actual.id_periodo, p_anterior.id_periodo
        ORDER BY
            p_actual.id_periodo;
            ";

            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    if($carrera=== 'all'){
                        $cohortes[] = $row['cohorte'];
                        $permanencias[] = floatval($row['permanencia']);
                    }else if($carrera === 'tec'){
                        $cohortesTec[] = $row['cohorte'];
                        $permanenciasTec[] = floatval($row['permanencia']);
                  }else if($carrera === 'ing'){
                    $cohortesIng[] = $row['cohorte'];
                    $permanenciasIng[] = floatval($row['permanencia']);
                }
                   
                }
            }
        }

        // Obtener datos por defecto (Todas las carreras)
         fetchData('all');
        fetchData('tec');
        fetchData('ing');
        // Imprimir el contenido de los arrays

               ?>


    // Crear la gráfica inicial
    var ctx = document.getElementById('permanencia-chart').getContext('2d');
    var chartTypeSelect = document.getElementById('chart-type');
    var chartCarreraSelect = document.getElementById('chart-carrera');
    var chart;

    // Función para crear el gráfico
    function createChart(chartType, labels, dataset) {
        if (chart) {
            chart.destroy(); // Destruir el gráfico existente si ya se ha creado
        }

        var data = {
            labels: labels,
            datasets: [dataset]
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
    // Función para actualizar el gráfico con la carrera seleccionada
    function updateChart() {
        var selectedType = chartTypeSelect.value;
        var selectedCarrera = chartCarreraSelect.value;

        var dataset = {
            label: selectedCarrera === 'all' ? 'TODAS LAS CARRERAS' : selectedCarrera === 'tec' ?
                'TECNOLOGIA EN SISTEMATIZACION DE DATOS' : 'INGENIERIA EN TELEMATICA',
            data: selectedCarrera === 'all' ? <?php echo json_encode($permanencias); ?> : selectedCarrera ===
                'tec' ? <?php echo json_encode($permanenciasTec); ?> : <?php echo json_encode($permanenciasIng); ?>,
            backgroundColor: selectedCarrera === 'all' ? 'rgba(255, 159, 49, 0.87)' : selectedCarrera === 'tec' ?
                'rgba(54, 162, 235, 0.5)' : 'rgba(0, 255, 0, 0.58)',
            borderColor: selectedCarrera === 'all' ? 'rgba(255, 159, 49, 0.87)' : selectedCarrera === 'tec' ?
                'rgba(54, 162, 235, 1)' : 'rgba(0, 255, 0, 0.58)',
            borderWidth: 1
        };

        createChart(selectedType, <?php echo json_encode($cohortes); ?>, dataset);
    }

    // Evento de cambio de tipo de gráfico
    chartTypeSelect.addEventListener('change', updateChart);

    // Evento de cambio de carrera
    chartCarreraSelect.addEventListener('change', updateChart);

    // Crear el gráfico inicial
    createChart(chartTypeSelect.value, <?php echo json_encode($cohortes); ?>, {
        label: 'Todas las carreras',
        data: <?php echo json_encode($permanencias); ?>,
        backgroundColor: 'rgba(255, 159, 49, 0.87)',
        borderColor: 'rgba(255, 159, 49, 0.87)',
        borderWidth: 1
    });
    </script>
    <script>
    function goBack() {
        window.history.back();
    }
    </script>

</body>

</html>