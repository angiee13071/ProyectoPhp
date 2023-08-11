<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promedio acumulado de deserción estudiantil</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>
    <?php include 'header.html'; ?>
    <div class="card">
        <div class="title">
            <h1>Promedio acumulado de deserción estudiantil</h1>
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

    $periodo = [];
    $desercion = [];
    
    $periodoTec = [];
    $desercionTec = [];
    
    $periodoIng = [];
    $desercionIng = [];
    
    function fetchData($carrera) {
     $query="SELECT ";
        global $conn, $periodo,$desercion,$periodoTec,$desercionTec,$periodoIng,$desercionIng;
        if ($carrera === 'all') {
            $query .= "
            CONCAT(periodo.anio, '-', periodo.semestre) AS periodos,
            CASE
                WHEN SUM(r.cantidad_retirados) > 0 THEN
                    ROUND((SUM(r.cantidad_retirados) / SUM(p.cantidad_primiparos)) * 100, 2)
                ELSE
                    0
            END AS tasa_desercion
        FROM
            periodo
        LEFT JOIN (
            SELECT
                id_periodo,
                COUNT(*) AS cantidad_retirados
            FROM
                retirado
            GROUP BY
                id_periodo
        ) r ON periodo.id_periodo = r.id_periodo
        LEFT JOIN (
            SELECT
                id_periodo,
                COUNT(*) AS cantidad_primiparos
            FROM
                primiparo
            GROUP BY
                id_periodo
        ) p ON periodo.id_periodo = p.id_periodo
        ORDER BY
            periodos
        LIMIT 0, 1000;
        
";
        }elseif($carrera === 'tec'){
            $query .=  "
            CONCAT(t1.anio, '-', t1.semestre) AS periodos,
            pr.nombre AS carrera,
            CASE
                WHEN t2.acum_primiparos > 0 THEN
                    ROUND((t2.acum_retirados / t2.acum_primiparos) * 100, 2)
                ELSE
                    0
            END AS tasa_desercion
        FROM
            periodo t1
        LEFT JOIN (
            SELECT
                p.id_periodo,
                SUM(DISTINCT pr.id_primiparo) AS acum_primiparos,
                SUM(DISTINCT r.id_retiro) AS acum_retirados
            FROM
                periodo p
            LEFT JOIN primiparo pr ON p.id_periodo = pr.id_periodo
            LEFT JOIN retirado r ON p.id_periodo = r.id_periodo
            GROUP BY
                p.id_periodo
        ) t2 ON t1.id_periodo = t2.id_periodo
        LEFT JOIN total t ON t1.id_periodo = t.id_periodo
        LEFT JOIN programa pr ON t.id_programa = pr.id_programa
        WHERE
            pr.nombre = 'TECNOLOGIA EN SISTEMATIZACION DE DATOS (CICLOS PROPEDEUTICOS)' 
        ORDER BY
            t1.anio, t1.semestre
        LIMIT 0, 1000;
";
    
        }elseif($carrera === 'ing'){
            $query .=  "
            CONCAT(t1.anio, '-', t1.semestre) AS periodos,
            pr.nombre AS carrera,
            CASE
                WHEN t2.acum_primiparos > 0 THEN
                    ROUND((t2.acum_retirados / t2.acum_primiparos) * 100, 2)
                ELSE
                    0
            END AS tasa_desercion
        FROM
            periodo t1
        LEFT JOIN (
            SELECT
                p.id_periodo,
                SUM(DISTINCT pr.id_primiparo) AS acum_primiparos,
                SUM(DISTINCT r.id_retiro) AS acum_retirados
            FROM
                periodo p
            LEFT JOIN primiparo pr ON p.id_periodo = pr.id_periodo
            LEFT JOIN retirado r ON p.id_periodo = r.id_periodo
            GROUP BY
                p.id_periodo
        ) t2 ON t1.id_periodo = t2.id_periodo
        LEFT JOIN total t ON t1.id_periodo = t.id_periodo
        LEFT JOIN programa pr ON t.id_programa = pr.id_programa
        WHERE
            pr.nombre = 'INGENIERIA EN TELEMATICA (CICLOS PROPEDEUTICOS)' 
        ORDER BY
            t1.anio, t1.semestre
        LIMIT 0, 1000;
           
        
        ";
    
        }
       
       
        $result = $conn->query($query);
        
       
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                if($carrera=== 'all'){
                    $periodo[] = $row['periodos'];
                    $desercion[] = $row['tasa_desercion'];
                                        
                }else if($carrera === 'tec'){
                    $periodoTec[] = $row['periodos'];
                    $desercionTec[] = $row['tasa_desercion'];
              }else if($carrera === 'ing'){
                $periodoIng[] = $row['periodos'];
                $desercionIng[] = $row['tasa_desercion'];
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

        // Obtener el elemento del eje Y
        var yAxis = chart.options.scales.y;
        if (selectedCarrera === 'all') {
            labels = <?php echo json_encode($periodo); ?>;
        } else if (selectedCarrera === 'tec') {
            labels = <?php echo json_encode($periodoTec); ?>;
        } else if (selectedCarrera === 'ing') {
            labels = <?php echo json_encode($periodoIng); ?>;
        }

        if (selectedCarrera === 'all') {
            dataset = {
                label: 'Deserción todas las carreras',
                data: <?php echo json_encode($desercion); ?>,
                backgroundColor: 'rgba(255, 159, 49, 0.87)',
                borderColor: 'rgba(255, 159, 49, 0.87)',
                borderWidth: 1
            };
        } else if (selectedCarrera === 'tec') {
            dataset = {
                label: 'Deserción TECNOLOGIA EN SISTEMATIZACION DE DATOS',
                data: <?php echo json_encode($desercionTec); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            };
        } else if (selectedCarrera === 'ing') {
            dataset = {
                label: 'Deserción INGENIERIA EN TELEMATICA (CICLOS PROPEDEUTICOS)',
                data: <?php echo json_encode($desercionIng); ?>,
                backgroundColor: 'rgba(0, 255, 0, 0.58)',
                borderColor: 'rgba(0, 255, 0, 0.58)',
                borderWidth: 1
            };
        }


        createChart(selectedType, labels, dataset);


    }



    // Evento de cambio de tipo de gráfico
    chartTypeSelect.addEventListener('change', updateChart);

    // Evento de cambio de carrera
    chartCarreraSelect.addEventListener('change', updateChart);

    // Crear el gráfico inicial
    createChart(chartTypeSelect.value, <?php echo json_encode($periodo); ?>, {
        label: 'Todas las carreras',
        data: <?php echo json_encode($desercion); ?>,
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