<?php
include "ConexionBD.php"; // Incluir el archivo de conexión a la base de datos
// Crear una instancia de la clase DatabaseConnection
$dbConnection = new DatabaseConnection();
$conn = $dbConnection->getDBConnection();
$labels = [];
$data = [];
$selectedYear = isset($_GET['year']) ? $_GET['year'] : 'all';
// Obtener los datos generales de la tabla estudiante

$query = "SELECT e.carrera, e.estrato, e.localidad, e.tipo_inscripcion, e.estado, e.promedio, e.pasantia, e.tipo_icfes, e.puntaje_icfes,
p.anio
FROM estudiante e
JOIN graduado g ON e.id_estudiante = g.id_estudiante
JOIN periodo p ON g.id_periodo = p.id_periodo";
if ($selectedYear && $selectedYear !== 'all') {
    $query .= " WHERE p.anio = '$selectedYear'";
   
}
$query .= "
UNION ALL
SELECT e.carrera, e.estrato, e.localidad, e.tipo_inscripcion, e.estado, e.promedio, e.pasantia, e.tipo_icfes, e.puntaje_icfes,
p.anio
FROM estudiante e
JOIN matriculado m ON e.id_estudiante = m.id_estudiante
JOIN periodo p ON m.id_periodo = p.id_periodo";
if ($selectedYear && $selectedYear !== 'all') {
    $query .= " WHERE p.anio = '$selectedYear'";
   
}
$query .= "
UNION ALL
SELECT e.carrera, e.estrato, e.localidad, e.tipo_inscripcion, e.estado, e.promedio, e.pasantia, e.tipo_icfes, e.puntaje_icfes,
p.anio
FROM estudiante e
JOIN admitido a ON e.id_estudiante = a.id_estudiante
JOIN periodo p ON a.id_periodo = p.id_periodo
";
if ($selectedYear && $selectedYear !== 'all') {
    $query .= " WHERE p.anio = '$selectedYear'";
   
}
// echo "$query";

$result = $conn->query($query);
$carreras= array();
$estratos= array();
$localidades = array();
// $generos = array();
$tiposInscripcion = array();
$estados = array();
$promedios = array();
$pasantias = array();
$tipos_icfes= array();
$puntajes_icfes= array();
$anios= array();
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $carreras[] = $row['carrera'];
    $estratos[] = $row['estrato'];
    $localidades[] = $row['localidad'];
    // $generos[] = $row['genero'];
    $tiposInscripcion[] = $row['tipo_inscripcion'];
    $estados[] = $row['estado'];
    $promedios[] = $row['promedio'];
    $pasantias[] = $row['pasantia'];
    $tipos_icfes[] = $row['tipo_icfes'];
    $puntajes_icfes[] = $row['puntaje_icfes'];
    $anios[] = $row['anio'];
    if (isset($row['pasantia'])) {
        $pasantias[] = $row['pasantia'];
    } else {
        $pasantias[] = ''; // O puedes asignarle un valor predeterminado en caso de que no exista.
    }
  }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas Estudiantiles</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <?php include 'header.html'; ?>
    <div class="card">
        <div class="title">
            <h1>Estadísticas Estudiantiles</h1>
        </div>
        <div class="chart-container">
            <canvas id="datos-generales-chart"></canvas>
        </div>
        <select id="data-type">
            <option value="carrera">Carrera</option>
            <option value="estrato">Estrato</option>
            <option value="localidad">Localidad</option>
            <!-- <option value="genero">Género</option> -->
            <option value="tipo_inscripcion">Tipo de Inscripción</option>
            <option value="estado">Estado del Estudiante</option>
            <option value="promedios">Promedios</option>
            <option value="pasantias">Pasantías</option>
            <option value="tipo_icfes">tipo_icfes</option>
            <option value="puntaje_icfes">Puntaje Icfes</option>
        </select>
        <div>
            <!-- <select id="year-select">
                <option value="all">Todos los años</option>
                <?php
    // foreach (array_unique($anios) as $anio) {
    //     echo '<option value="' . $anio . '">' . $anio . '</option>';
    // }
    ?>
            </select> -->
        </div>

        <select id="chart-type-p" class="oculto" style="display: none;">
            <option value="doughnut">Gráfico de Torta</option>
        </select>
        <button class="arrow-button" onclick="goBack()">&#8592;</button>
    </div>

    <script>
    // Obtener los datos generales para el gráfico de torta
    var dataTypeSelect = document.getElementById('data-type');
    var chartTypeSelectD = document.getElementById('chart-type-p');
    var ctx = document.getElementById('datos-generales-chart').getContext('2d');
    var chart;
    // var yearSelect = document.getElementById('year-select');
    // Función para contar la frecuencia de los datos
    function contarFrecuencia(datos) {
        var frecuencia = {};
        datos.forEach(function(dato) {
            if (!frecuencia[dato]) {
                frecuencia[dato] = 1;
            } else {
                frecuencia[dato]++;
            }
        });
        return frecuencia;
    }
    // document.addEventListener('DOMContentLoaded', function() {
    //     var dataTypeSelect = document.getElementById('data-type');
    //     var yearSelect = document.getElementById('year');
    //     var chartTypeSelectD = document.getElementById('chart-type-p');
    //     var ctx = document.getElementById('datos-generales-chart').getContext('2d');
    //     var chart;
    //     createChart();
    // });

    // Función para crear el gráfico de torta
    function createChart() {
        var selectedDataType = dataTypeSelect.value;
        // var selectedYear = yearSelect.options[yearSelect.selectedIndex].value;


        // Obtener los datos correspondientes al tipo seleccionado
        var datos = [];
        switch (selectedDataType) {
            case 'carrera':
                datos = <?php echo json_encode($carreras); ?>;
                break;
            case 'estrato':
                datos = <?php echo json_encode($estratos); ?>;
                break;

            case 'localidad':
                datos = <?php echo json_encode($localidades); ?>;
                break;

            case 'tipo_inscripcion':
                datos = <?php echo json_encode($tiposInscripcion); ?>;
                break;
            case 'estado':
                datos = <?php echo json_encode($estados); ?>;
                break;
            case 'promedios':
                datos = <?php echo json_encode($promedios); ?>;
                break;
            case 'pasantias':
                datos = <?php echo json_encode($pasantias); ?>;
                break;
            case 'tipo_icfes':
                datos = <?php echo json_encode($tipos_icfes); ?>;
                break;
            case 'puntaje_icfes':
                datos = <?php echo json_encode($puntajes_icfes); ?>;
                break;

        }

        // Contar la frecuencia de cada dato
        var frecuenciaDatos = contarFrecuencia(datos);

        // Crear los datos para el gráfico de torta
        var datosGenerales = {
            labels: Object.keys(frecuenciaDatos),
            datasets: [{
                data: Object.values(frecuenciaDatos),
                backgroundColor: [
                    'rgba(251, 253, 79, 0.74)',
                    'rgba(67, 66, 66, 0.19)',
                    'rgba(255, 99, 132, 0.5)',
                    'rgba(54, 162, 235, 0.5)',
                    'rgba(153, 102, 255, 0.5)',
                    'rgba(255, 206, 86, 0.5)',
                    'rgba(255, 159, 64, 0.5)',
                    'rgba(50, 168, 82, 0.5)',
                    'rgba(195, 61, 61, 0.5)',
                    'rgba(88, 89, 91, 0.5)',
                    'rgba(75, 192, 192, 0.5)'
                ],
                borderColor: [
                    'rgba(228, 134, 5, 1)',
                    'rgba(67, 66, 66, 0.74)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(50, 168, 82, 1)',
                    'rgba(195, 61, 61, 1)',
                    'rgba(88, 89, 91, 1)',
                    'rgba(75, 192, 192, 1)'
                ],
                borderWidth: 1
            }]
        };

        // Crear el gráfico de torta
        var chartType = chartTypeSelectD.value;
        var options = {
            responsive: true,
            maintainAspectRatio: false
        };

        if (chart) {
            chart.destroy(); // Destruir el gráfico existente si ya se ha creado
        }
        chart = new Chart(ctx, {
            type: chartType,
            data: datosGenerales,
            options: options
        });
    }

    // Evento de cambio de tipo de dato
    dataTypeSelect.addEventListener('change', createChart);

    // Evento de cambio de tipo de gráfico
    chartTypeSelectD.addEventListener('change', createChart);
    //Evento de cambio de anio
    // yearSelect.addEventListener('change', createChart);
    // Crear el gráfico inicial
    createChart();
    //$conn->close();
    </script>
    <script>
    function goBack() {
        window.history.back();
    }
    </script>
</body>

</html>