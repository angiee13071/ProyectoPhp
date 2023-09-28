<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estado de la permanencia estudiantil</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>
    <?php include 'header.html'; ?>
    <div class="card">
        <div class="title">
            <h1>Estado de la permanencia estudiantil</h1>
        </div>
        <div class="textSmall" style="font-size:1rem;">
            * La retención es la capacidad de mantener a los estudiantes y evitar que abandonen sus
            estudios y la tasa de graduación indica cuántos terminan sus estudios con éxito.
        </div>
        <div class="chart-container">
            <canvas id="retencion-chart"></canvas>
        </div>
        <div>
            <select id="chart-type">
                <option value="line">Gráfico de líneas</option>
                <option value="bar">Gráfico de columnas</option>
            </select>
        </div>
        <div>
            <select id="chart-metric">
                <option value="retencion">Tasa de Retención</option>
                <option value="graduacion">Tasa de Graduación</option>
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
// Crear una instancia de la clase DatabaseConnection
$dbConnection = new DatabaseConnection();
$conn = $dbConnection->getDBConnection();
    $periodo = [];
    $retencion = [];
    $graduacion = [];
    $periodoTec = [];
    $retencionTec = [];
    $graduacionTec = [];
    $periodoIng = [];
    $retencionIng = [];
    $graduacionIng = [];
    
    function fetchData($carrera) {
     $query="";
        global $conn, $periodo,$retencion,$graduacion,$retencionTec,$graduacionTec,$retencionIng,$graduacionIng,$periodoTec,$periodoIng;
        if ($carrera === 'all') {
            $query = "SELECT
   
            CONCAT(p_actual.anio, '-', p_actual.semestre) AS periodo,
            CONCAT(p_anterior.anio, '-', p_anterior.semestre) AS periodo_anterior,
            SUM(t_actual.matriculados) AS matriculados_actual,
            
            IFNULL(LAG(t_anterior.matriculados) OVER (ORDER BY p_actual.id_periodo), 0) AS matriculados_anterior,
            LEAST((SUM(t_actual.matriculados) / IFNULL(LAG(t_anterior.matriculados) OVER (ORDER BY p_actual.id_periodo), 1)) * 100, 100) AS tasa_retencion,
         LEAST((SUM(t_actual.graduados) / IFNULL(LAG(t_anterior.matriculados) OVER (ORDER BY p_actual.id_periodo), 1)) * 100, 100) AS tasa_graduacion,
            t_actual.id_programa
        FROM
            total t_actual  
        JOIN
            periodo p_actual ON t_actual.id_periodo = p_actual.id_periodo
        LEFT JOIN
            periodo p_anterior ON p_actual.id_periodo = p_anterior.id_periodo + 1
        LEFT JOIN
            total t_anterior ON t_anterior.id_periodo = p_anterior.id_periodo AND t_actual.id_programa = t_anterior.id_programa
        GROUP BY
            p_actual.id_periodo, p_anterior.id_periodo, t_actual.id_programa
        ORDER BY
            p_actual.id_periodo;
        ";
        }elseif($carrera === 'tec'){
            $query = "
            SELECT
            CONCAT(p_actual.anio, '-', p_actual.semestre) AS periodo,
            CONCAT(p_anterior.anio, '-', p_anterior.semestre) AS periodo_anterior,
            SUM(t_actual.matriculados) AS matriculados_actual,
            IFNULL(LAG(t_anterior.matriculados) OVER (ORDER BY p_actual.id_periodo), 0) AS matriculados_anterior,
            LEAST((SUM(t_actual.matriculados) / IFNULL(LAG(t_anterior.matriculados) OVER (ORDER BY p_actual.id_periodo), 1)) * 100, 100) AS tasa_retencion,
            LEAST((SUM(t_actual.graduados) / IFNULL(LAG(t_anterior.matriculados) OVER (ORDER BY p_actual.id_periodo), 1)) * 100, 100) AS tasa_graduacion,
            t_actual.id_programa
        FROM
            total t_actual   
        JOIN
            periodo p_actual ON t_actual.id_periodo = p_actual.id_periodo
        LEFT JOIN
            periodo p_anterior ON p_actual.id_periodo = p_anterior.id_periodo + 1
        LEFT JOIN
            total t_anterior ON t_anterior.id_periodo = p_anterior.id_periodo AND t_actual.id_programa = t_anterior.id_programa
        WHERE
            t_actual.id_programa = 578
        GROUP BY
            p_actual.id_periodo, p_anterior.id_periodo, t_actual.id_programa
        ORDER BY
            p_actual.id_periodo;
        ";
        }elseif($carrera === 'ing'){
            $query =  "
            SELECT
    CONCAT(p_actual.anio, '-', p_actual.semestre) AS periodo,
    CONCAT(p_anterior.anio, '-', p_anterior.semestre) AS periodo_anterior,
    SUM(t_actual.matriculados) AS matriculados_actual,
    IFNULL(LAG(t_anterior.matriculados) OVER (ORDER BY p_actual.id_periodo), 0) AS matriculados_anterior,
    LEAST((SUM(t_actual.matriculados) / IFNULL(LAG(t_anterior.matriculados) OVER (ORDER BY p_actual.id_periodo), 1)) * 100, 100) AS tasa_retencion,
    LEAST((SUM(t_actual.graduados) / IFNULL(LAG(t_anterior.matriculados) OVER (ORDER BY p_actual.id_periodo), 1)) * 100, 100) AS tasa_graduacion,
    t_actual.id_programa
FROM
    total t_actual   
JOIN
    periodo p_actual ON t_actual.id_periodo = p_actual.id_periodo
LEFT JOIN
    periodo p_anterior ON p_actual.id_periodo = p_anterior.id_periodo + 1
LEFT JOIN
    total t_anterior ON t_anterior.id_periodo = p_anterior.id_periodo AND t_actual.id_programa = t_anterior.id_programa
WHERE
    t_actual.id_programa = 678
GROUP BY
    p_actual.id_periodo, p_anterior.id_periodo, t_actual.id_programa
ORDER BY
    p_actual.id_periodo;
        ";
    
        }
       
        $result = $conn->query($query);
        
       
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                if($carrera=== 'all'){
                    $periodo[] = $row['periodo'];
                    $retencion[] = $row['tasa_retencion'];
                    $graduacion[] = $row['tasa_graduacion'];
                    
                }else if($carrera === 'tec'){
                    $periodoTec[] = $row['periodo'];
                    $retencionTec[] = $row['tasa_retencion'];
                    $graduacionTec[] = $row['tasa_graduacion'];
              }else if($carrera === 'ing'){
                $periodoIng[] = $row['periodo'];
                $retencionIng[] = $row['tasa_retencion'];
                $graduacionIng[] = $row['tasa_graduacion'];
            }
               
            }
         
        }
    }
    
       // Obtener datos por defecto (Todas las carreras)
        fetchData('all');
        fetchData('tec');
        fetchData('ing');
  
    ?>

    // Crear la gráfica inicial
    var ctx = document.getElementById('retencion-chart').getContext('2d');
    var chartTypeSelect = document.getElementById('chart-type');
    var chartMetricSelect = document.getElementById('chart-metric'); // Nuevo elemento de filtro
    var chartCarreraSelect = document.getElementById('chart-carrera');
    var chart;

    // Función para crear el gráfico de retención
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
    // Función para actualizar el gráfico con la carrera seleccionada
    function updateChart() {
        var selectedType = chartTypeSelect.value;
        var selectedCarrera = chartCarreraSelect.value;
        var dataset = [];
        var labels; // Agregar esta línea para definir 'labels'
        var selectedMetric = chartMetricSelect.value;
        // Obtener el elemento del eje Y
        var yAxis = chart.options.scales.y;
        if (selectedCarrera === 'all') {
            labels = <?php echo json_encode($periodo); ?>;
        } else if (selectedCarrera === 'tec') {
            labels = <?php echo json_encode($periodoTec); ?>;
        } else if (selectedCarrera === 'ing') {
            labels = <?php echo json_encode($periodoIng); ?>;
        }
        if (selectedMetric === 'retencion') {
            if (selectedCarrera === 'all') {
                dataset = {
                    label: 'Retención todas las carreras',
                    data: <?php echo json_encode($retencion); ?>,
                    backgroundColor: 'rgba(255, 159, 49, 0.87)',
                    borderColor: 'rgba(255, 159, 49, 0.87)',
                    borderWidth: 1
                };
            } else if (selectedCarrera === 'tec') {
                dataset = {
                    label: 'Retención TECNOLOGIA EN SISTEMATIZACION DE DATOS',
                    data: <?php echo json_encode($retencionTec); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                };
            } else if (selectedCarrera === 'ing') {
                dataset = {
                    label: 'Retención INGENIERIA EN TELEMATICA (CICLOS PROPEDEUTICOS)',
                    data: <?php echo json_encode($retencionIng); ?>,
                    backgroundColor: 'rgba(0, 255, 0, 0.58)',
                    borderColor: 'rgba(0, 255, 0, 0.58)',
                    borderWidth: 1
                };
            }

        } else if (selectedMetric === 'graduacion') {
            if (selectedCarrera === 'all') {
                dataset = {
                    label: 'Tasa de Graduación todas las carreras',
                    data: <?php echo json_encode($graduacion); ?>,
                    backgroundColor: 'rgba(255, 159, 49, 0.87)',
                    borderColor: 'rgba(255, 159, 49, 0.87)',
                    borderWidth: 1
                };
            } else if (selectedCarrera === 'tec') {
                dataset = {
                    label: 'Tasa de Graduación TECNOLOGIA EN SISTEMATIZACION DE DATOS',
                    data: <?php echo json_encode($graduacionTec); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                };
            } else if (selectedCarrera === 'ing') {
                dataset = {
                    label: 'Tasa de Graduación INGENIERIA EN TELEMATICA (CICLOS PROPEDEUTICOS)',
                    data: <?php echo json_encode($retencionIng); ?>,
                    backgroundColor: 'rgba(0, 255, 0, 0.58)',
                    borderColor: 'rgba(0, 255, 0, 0.58)',
                    borderWidth: 1
                };
            }



        }

        createChart(selectedType, labels, dataset);


    }


    chartMetricSelect.addEventListener('change', updateChart);
    // Evento de cambio de tipo de gráfico
    chartTypeSelect.addEventListener('change', updateChart);

    // Evento de cambio de carrera
    chartCarreraSelect.addEventListener('change', updateChart);

    // Crear el gráfico inicial
    createChart(chartTypeSelect.value, <?php echo json_encode($periodo); ?>, {
        label: 'Todas las carreras',
        data: <?php echo json_encode($retencion); ?>,
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