<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Deserción por año</title>
  <link rel="stylesheet" href="prueba.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
  <div class="card">
    <div class="title">
      <h1>Deserción por año:</h1>
    </div>
    <div class="chart-container">
      <canvas id="desercion-chart"></canvas>
    </div>
    <select id="chart-type">
      <option value="line">Gráfico de líneas</option>
      <option value="bar">Gráfico de barras</option>
    </select>
    <button class="arrow-button" onclick="goBack()">&#8592;</button>
  </div>

  <script>
    // Obtener los datos de la tabla 'total'
    <?php
    include "conexion.php"; // Incluye el archivo de conexión a la base de datos

    $anios = [];
    $desertores = [];
    $primiparos = [];

    $query = "SELECT periodo.anio, 
              ((SELECT COUNT(*) FROM matriculado WHERE id_periodo = periodo.id_periodo) - 
              (SELECT COUNT(*) FROM graduado WHERE id_periodo = periodo.id_periodo) +
              (SELECT COUNT(*) FROM primiparo WHERE id_periodo = periodo.id_periodo)) AS desertores,
              (SELECT COUNT(*) FROM primiparo WHERE id_periodo = periodo.id_periodo) AS primiparos
              FROM periodo
              ORDER BY periodo.anio";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $anios[] = $row['anio'];
        $desertores[] = $row['desertores'];
        $primiparos[] = $row['primiparos'];
      }
    }

    $conn->close();
    ?>

    // Crear la gráfica inicial
    var ctx = document.getElementById('desercion-chart').getContext('2d');
    var chartTypeSelect = document.getElementById('chart-type');
    var chart;

    // Función para crear el gráfico
    function createChart(chartType) {
      if (chart) {
        chart.destroy(); // Destruir el gráfico existente si ya se ha creado
      }

      var data = {
        labels: <?php echo json_encode($anios); ?>,
        datasets: [{
          label: 'Desertores',
          data: <?php echo json_encode($desertores); ?>,
          backgroundColor: 'rgba(54, 162, 235, 0.5)',
          borderColor: 'rgba(54, 162, 235, 1)',
          borderWidth: 1
        }, {
          label: 'Primíparos',
          data: <?php echo json_encode($primiparos); ?>,
          backgroundColor: 'rgba(255, 99, 132, 0.5)',
          borderColor: 'rgba(255, 99, 132, 1)',
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
