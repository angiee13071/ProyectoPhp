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
        <button class="arrow-button" onclick="goBack()">&#8592;</button>
    </div>

    <script>
    // Obtener los datos de la tabla 'graduado' y 'estudiante'
    <?php
        include "ConexionBD.php"; // Incluye el archivo de conexión a la base de datos
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
                    if($carrera=== 'all'){
                        $cohortes[] = $row['periodo_actual'];
                        $permanencias[] = floatval($row['permanencia']);
                    }else if($carrera === 'tec'){
                        $cohortesTec[] = $row['periodo_actual'];
                        $permanenciasTec[] = floatval($row['permanencia']);
                  }else if($carrera === 'ing'){
                    $cohortesIng[] = $row['periodo_actual'];
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
    var chart;

    // Función para crear el gráfico
    function createChart(chartType, labels, datasets) {
        if (chart) {
            chart.destroy(); // Destruir el gráfico existente si ya se ha creado
        }

        var data = {
            labels: labels,
            datasets: datasets
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
    createChart(chartTypeSelect.value, <?php echo json_encode($cohortes); ?>, [{
            label: 'Todas las carreras',
            data: <?php echo json_encode($permanencias); ?>,
            backgroundColor: 'rgba(255, 159, 49, 0.87)',
            borderColor: 'rgba(255, 159, 49, 0.87)',
            borderWidth: 1
        },
        {
            label: 'Tecnología en sistematización de datos',
            data: <?php echo json_encode($permanenciasTec); ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.5)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        },
        {
            label: 'Ingeniería telemática',
            data: <?php echo json_encode($permanenciasIng); ?>,
            backgroundColor: 'rgba(0, 255, 0, 0.58)',
            borderColor: 'rgba(0, 255, 0, 0.58)',
            borderWidth: 1
        }

    ]);
    // Evento de cambio de tipo de gráfico
    chartTypeSelect.addEventListener('change', function() {
        var selectedType = chartTypeSelect.value;
        createChart(selectedType, <?php echo json_encode($cohortes); ?>,
            <?php echo json_encode($permanencias); ?>);
    });

    // Evento de cambio de carrera
    // chartSelectCarrer.addEventListener('change', function() {
    //     updateChart();
    // });

    // Crear el gráfico inicial
    // createChart(chartTypeSelect.value, <?php echo json_encode($cohortes); ?>,<?php echo json_encode($permanencias); ?>);
    </script>
    <script>
    function goBack() {
        window.history.back();
    }
    </script>

</body>

</html>