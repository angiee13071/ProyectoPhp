<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deserción estudiantil por año</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>
    <?php include 'header.html'; ?>
    <div class="card">
        <div class="title">
            <h1>Deserción estudiantil por año</h1>
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
            
    CONCAT(periodo.anio) AS periodos,
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
ORDER BY periodos
LIMIT 0, 1000;
";
        }elseif($carrera === 'tec'){
            $query .=  " CONCAT(periodo.anio) AS periodos,
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
        WHERE periodo.id_periodo IN (
            SELECT id_periodo FROM estudiante WHERE carrera = 'TECNOLOGIA EN SISTEMATIZACION DE DATOS (CICLOS PROPEDEUTICOS)'
        )
        ORDER BY periodos
        LIMIT 0, 1000;";
    
        }elseif($carrera === 'ing'){
            $query .=  " CONCAT(periodo.anio) AS periodos,
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
        WHERE periodo.id_periodo IN (
            SELECT id_periodo FROM estudiante WHERE carrera = 'INGENIERIA EN TELEMATICA (CICLOS PROPEDEUTICOS)'
        )
        ORDER BY periodos
        LIMIT 0, 1000;";
    
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