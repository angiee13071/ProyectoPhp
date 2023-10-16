<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promedio desercion</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>
<?php include 'header.html'; ?>

<body>
    <div class="card">
        <div class="title">
            <h1>Promedio acumulado de deserción</h1>
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
        <button class="export-button" onclick="exportChart()">
            Exportar Gráfica <i class="fas fa-download"></i>
        </button>
        <button class="arrow-button" onclick="goBack()">&#8592;</button>
    </div>

    <script>
    // Obtener los datos de la tabla 'graduado' y 'estudiante'
    <?php
        include "../Modelo/ConexionBD.php"; // Incluye el archivo de conexión a la base de datos
// Crear una instancia de la clase DatabaseConnection
$dbConnection = new DatabaseConnection();
$conn = $dbConnection->getDBConnection();
        //grafica 1
        $cohortes = [];
        $desercions = [];
        //grafica 2
        $cohortesTec = [];
        $desercionsTec = [];
        //grafica 3
        $cohortesIng = [];
        $desercionsIng = [];
 

        // Función para obtener los datos dependiendo de la carrera seleccionada
        function fetchData($carrera) {
            global $conn, $cohortes, $desercions,$cohortesTec,$desercionsTec,$cohortesIng,$desercionsIng;

            $query = "SELECT
         ";

            if ($carrera === 'all') {
              $query .="     CONCAT(periodo.anio, '-', periodo.semestre) AS periodos,
              NULL AS id_programa,
              SUM(t.retirados) AS desertores_total,
              SUM(t.matriculados) AS matriculados_total,
              CAST(
                COALESCE(
                  CASE
                    WHEN SUM(t.retirados) = 0 OR SUM(t.matriculados) = 0 THEN 0
                    ELSE LEAST((SUM(t.retirados) / NULLIF(SUM(t_anterior.matriculados), 0)) * 100, 100)
                  END,
                  0
                ) AS UNSIGNED
              ) AS tasa_desercion
            FROM total t
            JOIN periodo ON t.id_periodo = periodo.id_periodo
            LEFT JOIN (
              SELECT id_programa, matriculados, id_periodo
              FROM total
            ) AS t_anterior ON t.id_programa = t_anterior.id_programa AND t.id_periodo = t_anterior.id_periodo + 1
            GROUP BY periodo.anio, periodo.cohorte
            ORDER BY periodos
            LIMIT 0, 1000;";
            }elseif($carrera === 'tec'){
                $query .= " CONCAT(periodo.anio, '-', periodo.semestre) AS periodos,
                NULL AS id_programa,
                SUM(t.retirados) AS desertores_total,
                SUM(t.matriculados) AS matriculados_total,
                CAST(
                  COALESCE(
                    CASE
                      WHEN SUM(t.retirados) = 0 OR SUM(t.matriculados) = 0 THEN 0
                      ELSE LEAST((SUM(t.retirados) / NULLIF(SUM(t_anterior.matriculados), 0)) * 100, 100)
                    END,
                    0
                  ) AS UNSIGNED
                ) AS tasa_desercion
              FROM total t
              JOIN periodo ON t.id_periodo = periodo.id_periodo
              LEFT JOIN (
                SELECT id_programa, matriculados, id_periodo
                FROM total
              ) AS t_anterior ON t.id_programa = t_anterior.id_programa AND t.id_periodo = t_anterior.id_periodo + 1
              WHERE t.id_programa = 578 -- Filtrar por id_programa igual a 578
              GROUP BY periodo.anio, periodo.cohorte
              ORDER BY periodos
              LIMIT 0, 1000;
              ";
            }elseif($carrera === 'ing'){
                $query .= " CONCAT(periodo.anio, '-', periodo.semestre) AS periodos,
                NULL AS id_programa,
                SUM(t.retirados) AS desertores_total,
                SUM(t.matriculados) AS matriculados_total,
                CAST(
                  COALESCE(
                    CASE
                      WHEN SUM(t.retirados) = 0 OR SUM(t.matriculados) = 0 THEN 0
                      ELSE LEAST((SUM(t.retirados) / NULLIF(SUM(t_anterior.matriculados), 0)) * 100, 100)
                    END,
                    0
                  ) AS UNSIGNED
                ) AS tasa_desercion
              FROM total t
              JOIN periodo ON t.id_periodo = periodo.id_periodo
              LEFT JOIN (
                SELECT id_programa, matriculados, id_periodo
                FROM total
              ) AS t_anterior ON t.id_programa = t_anterior.id_programa AND t.id_periodo = t_anterior.id_periodo + 1
              WHERE t.id_programa = 678 -- Filtrar por id_programa igual a 578
              GROUP BY periodo.anio, periodo.cohorte
              ORDER BY periodos
              LIMIT 0, 1000;";
            }

         

            $result = $conn->query($query);

          
            if ($result) {
                $sumaPonderada = 0;
                $totalPeriodos = 0;
                while ($row = $result->fetch_assoc()) {
                    $totalPeriodos++;
                    $sumaPonderada += floatval($row['tasa_desercion']); 
                    $promedioPonderado = ($totalPeriodos > 0) ? ($sumaPonderada / $totalPeriodos) : 0;
                    if($carrera=== 'all'){
                        $cohortes[] = $row['periodos'];
                        $desercions[] = $promedioPonderado;
        
    } else if($carrera === 'tec'){
    $cohortesTec[] = $row['periodos'];
    $desercionsTec[] = $promedioPonderado;
    } else if($carrera === 'ing'){
    $cohortesIng[] = $row['periodos'];
    $desercionsIng[] = $promedioPonderado;
    }
    }
    } else {
    echo "<script>
    console.error('Error en la consulta: " . $conn->error . "');
    </script>";
    }

    }

    // Obtener datos por defecto (Todas las carreras)
    fetchData('all');
    fetchData('tec');
    fetchData('ing');
    ?>

    // Crear la gráfica inicial
    var ctx = document.getElementById('desercion-chart').getContext('2d');
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
    function exportChart() {
    var chartContainer = document.querySelector('.chart-container');
    html2canvas(chartContainer).then(function(canvas) {
    var downloadLink = document.createElement('a');
    downloadLink.href = canvas.toDataURL(
    'image/png');
    downloadLink.download = 'Deserción_promedio.png';
    downloadLink.click();
    });
    }
    // Función para actualizar el gráfico con la carrera seleccionada
    // Función para actualizar el gráfico con la carrera seleccionada
    function updateChart() {
    var selectedType = chartTypeSelect.value;
    var selectedCarrera = chartCarreraSelect.value;
    var dataset;

    if (selectedCarrera === 'all') {
    dataset = {
    label: 'TODAS LAS CARRERAS',
    data: <?php echo json_encode($desercions); ?>,
    backgroundColor: 'rgba(255, 159, 49, 0.87)',
    borderColor: 'rgba(255, 159, 49, 0.87)',
    borderWidth: 1
    };
    } else if (selectedCarrera === 'tec') {
    dataset = {
    label: 'TECNOLOGIA EN SISTEMATIZACION DE DATOS',
    data: <?php echo json_encode($desercionsTec); ?>,
    backgroundColor: 'rgba(54, 162, 235, 0.5)',
    borderColor: 'rgba(54, 162, 235, 1)',
    borderWidth: 1
    };
    } else if (selectedCarrera === 'ing') {
    dataset = {
    label: 'INGENIERIA EN TELEMATICA',
    data: <?php echo json_encode($desercionsIng); ?>,
    backgroundColor: 'rgba(0, 255, 0, 0.58)',
    borderColor: 'rgba(0, 255, 0, 0.58)',
    borderWidth: 1
    };
    }

    createChart(selectedType, <?php echo json_encode($cohortes); ?>, dataset);
    }


    // Evento de cambio de tipo de gráfico
    chartTypeSelect.addEventListener('change', updateChart);

    // Evento de cambio de carrera
    chartCarreraSelect.addEventListener('change', updateChart);

    // Crear el gráfico inicial
    createChart(chartTypeSelect.value, <?php echo json_encode($cohortes); ?>, {
    label: 'Todas las carreras',
    data: <?php echo json_encode($desercions); ?>,
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