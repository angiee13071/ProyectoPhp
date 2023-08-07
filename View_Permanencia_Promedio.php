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

<?php include 'header.html'; ?>

<body>
    <div class="card">
        <div class="title">
            <h1>Promedio Acumulado</h1>
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
            <button class="arrow-button" onclick="goBack()">&#8592;</button>
        </div>

        <!-- Mostrar el promedio ponderado -->
        <?php
        include "ConexionBD.php"; // Incluye el archivo de conexión a la base de datos

        $cohortes = [];
        $promediosPonderados = [];

        // Realiza la consulta con la condición de carrera
        $carrera = isset($_GET['carrera']) ? $_GET['carrera'] : 'all';

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
        LEFT JOIN estudiante e ON m.id_estudiante = e.id_estudiante ";

        if ($carrera === 'all') {
            $query .= "WHERE m.estado_matricula = 'ESTUDIANTE MATRICULADO' ";
        } elseif ($carrera === 'tec') {
            $query .= "WHERE m.estado_matricula = 'ESTUDIANTE MATRICULADO' AND e.carrera = 'TECNOLOGIA EN SISTEMATIZACION DE DATOS (CICLOS PROPEDEUTICOS)' ";
        } elseif ($carrera === 'ing') {
            $query .= "WHERE m.estado_matricula = 'ESTUDIANTE MATRICULADO' AND e.carrera = 'INGENIERIA EN TELEMATICA (CICLOS PROPEDEUTICOS)' ";
        }

        $query .= "GROUP BY periodo_actual, periodo_anterior, p.id_periodo, p.cohorte, e.carrera 
            ORDER BY p.anio, p.semestre;";

        $result = $conn->query($query);
        if ($result->num_rows > 0) {
            $sumaPonderada = 0;
            $totalPeriodos = 0;
        
            while ($row = $result->fetch_assoc()) {
                $totalPeriodos++;
                $sumaPonderada += floatval($row['permanencia']); // Suma de las permanencias
                $promedioPonderado = ($totalPeriodos > 0) ? ($sumaPonderada / $totalPeriodos) : 0;
        
                $cohortes[] = $row['periodo_actual'];
                $promediosPonderados[] = floatval($promedioPonderado); // Convertir el valor a número usando floatval()
            }
        }

        $conn->close();
        ?>

        <!-- Fin del bloque para mostrar el promedio ponderado -->
    </div>

    <script>
    // Crear la gráfica inicial
    var ctx = document.getElementById('permanencia-chart').getContext('2d');
    var chartTypeSelect = document.getElementById('chart-type');
    var chartSelectCarrer = document.getElementById('chart-carrer');
    var chart;

    // Función para crear el gráfico
    function createChart(chartType) {
        if (chart) {
            chart.destroy(); // Destruir el gráfico existente si ya se ha creado
        }

        var data = {
            labels: <?php echo json_encode($cohortes); ?>,
            datasets: [{
                label: 'Promedio Ponderado hasta cada Período',
                data: <?php echo json_encode($promediosPonderados); ?>,
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