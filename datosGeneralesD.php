<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Datos Generales</title>
  <link rel="stylesheet" href="prueba.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
  <div class="card">
    <div class="title">
      <h1>Datos Generales</h1>
    </div>
    <div class="chart-container">
      <canvas id="datos-chart"></canvas>
    </div>
    <select id="dato-general">
      <option value="localidad">Localidad</option>
      <option value="genero">Género</option>
      <option value="tipo_inscripcion">Tipo de Inscripción</option>
      <option value="estado">Estado del Estudiante</option>
    </select>
    <button class="arrow-button" onclick="goBack()">&#8592;</button>
  </div>

  <script>
    // Obtener los datos del tipo de dato general seleccionado
    <?php
    include "conexion.php"; // Incluye el archivo de conexión a la base de datos

    $labels = [];
    $data = [];

    $datoGeneral = isset($_GET['dato_general']) ? $_GET['dato_general'] : 'localidad'; // Obtener el dato general seleccionado, predeterminado a 'localidad' si no se selecciona

    $query = "SELECT $datoGeneral, COUNT(*) as count FROM estudiante GROUP BY $datoGeneral";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $labels[] = $row[$datoGeneral];
        $data[] = $row['count'];
      }
    }

    $conn->close();
    ?>

    // Crear la gráfica inicial
    var ctx = document.getElementById('datos-chart').getContext('2d');
    var datoGeneralSelect = document.getElementById('dato-general');
    var chart;

    // Función para crear el gráfico
    function createChart() {
      if (chart) {
        chart.destroy(); // Destruir el gráfico existente si ya se ha creado
      }

      var data = {
        labels: <?php echo json_encode($labels); ?>,
        datasets: [{
          data: <?php echo json_encode($data); ?>,
          backgroundColor: getRandomColors(<?php echo count($data); ?>),
          borderColor: 'white',
          borderWidth: 1
        }]
      };

      var options = {
        responsive: true,
        maintainAspectRatio: false
      };

      chart = new Chart(ctx, {
        type: 'pie',
        data: data,
        options: options
      });
    }

    // Evento de cambio de dato general
    datoGeneralSelect.addEventListener('change', function() {
      var selectedDatoGeneral = datoGeneralSelect.value;
      window.location.href = 'datosGeneralesD.php?dato_general=' + selectedDatoGeneral;
    });

    // Función para obtener colores aleatorios
    function getRandomColors(numColors) {
      var colors = [];
      for (var i = 0; i < numColors; i++) {
        colors.push('#' + Math.floor(Math.random() * 16777215).toString(16));
      }
      return colors;
    }

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
