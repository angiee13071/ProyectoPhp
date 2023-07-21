<?php
include "conexion.php"; // Incluir el archivo de conexión a la base de datos
$labels = [];
$data = [];

// Obtener los datos generales de la tabla estudiante
$query = "SELECT localidad, genero, tipo_inscripcion, estado FROM estudiante";
$result = $conn->query($query);

$localidades = array();
$generos = array();
$tiposInscripcion = array();
$estados = array();

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $localidades[] = $row['localidad'];
    $generos[] = $row['genero'];
    $tiposInscripcion[] = $row['tipo_inscripcion'];
    $estados[] = $row['estado'];
  }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Deserción - Datos generales</title>
  <link rel="stylesheet" href="styles.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
  <div class="card">
    <div class="title">
      <h1>Datos Generales</h1>
    </div>
    <div class="chart-container">
      <canvas id="datos-generales-chart"></canvas>
    </div>
    <select id="data-type">
      <option value="localidad">Localidad</option>
      <option value="genero">Género</option>
      <option value="tipo_inscripcion">Tipo de Inscripción</option>
      <option value="estado">Estado del Estudiante</option>
    </select>
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

    // Función para crear el gráfico de torta
    function createChart() {
      var selectedDataType = dataTypeSelect.value;

      // Obtener los datos correspondientes al tipo seleccionado
      var datos = [];
      switch (selectedDataType) {
        case 'localidad':
          datos = <?php echo json_encode($localidades); ?>;
          break;
        case 'genero':
          datos = <?php echo json_encode($generos); ?>;
          break;
        case 'tipo_inscripcion':
          datos = <?php echo json_encode($tiposInscripcion); ?>;
          break;
        case 'estado':
          datos = <?php echo json_encode($estados); ?>;
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
            'rgba(255, 99, 132, 0.5)',
            'rgba(54, 162, 235, 0.5)',
            'rgba(255, 206, 86, 0.5)',
            'rgba(75, 192, 192, 0.5)',
            'rgba(153, 102, 255, 0.5)',
            'rgba(255, 159, 64, 0.5)',
            'rgba(50, 168, 82, 0.5)',
            'rgba(195, 61, 61, 0.5)',
            'rgba(88, 89, 91, 0.5)',
            'rgba(228, 134, 5, 0.5)',
            'rgba(94, 138, 141, 0.5)'
          ],
          borderColor: [
            'rgba(255, 99, 132, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)',
            'rgba(50, 168, 82, 1)',
            'rgba(195, 61, 61, 1)',
            'rgba(88, 89, 91, 1)',
            'rgba(228, 134, 5, 1)',
            'rgba(94, 138, 141, 1)'
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

    // Crear el gráfico inicial
    createChart();
  </script>
  <script>
    function goBack() {
      window.history.back();
    }
  </script>
</body>

</html>