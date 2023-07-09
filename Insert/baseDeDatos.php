<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Base de datos</title>
  <link rel="stylesheet" href="styles.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<table>
  <tr>
    <th>ID Estudiante</th>
    <th>Nombres</th>
    <th>Género</th>
    <th>Carrera</th>
    <th>Documento</th>
    <th>Estrato</th>
    <th>Localidad</th>
    <th>Género Género</th>
    <th>Tipo Inscripción</th>
    <th>Estado</th>
    <th>ID Programa</th>
  </tr>
  <?php
  // Incluir el archivo de conexión a la base de datos
  include "conexion.php";

  // Obtener la conexión a la base de datos
  $conn = getDBConnection();

  // Realizar la consulta a la base de datos
  $query = "SELECT * FROM estudiante";
  $result = mysqli_query($conn, $query);

  // Obtener y mostrar los datos en la tabla
  while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . $row['id_estudiante'] . "</td>";
    echo "<td>" . $row['nombres'] . "</td>";
    echo "<td>" . $row['genero'] . "</td>";
    echo "<td>" . $row['carrera'] . "</td>";
    echo "<td>" . $row['documento'] . "</td>";
    echo "<td>" . $row['estrato'] . "</td>";
    echo "<td>" . $row['localidad'] . "</td>";
    echo "<td>" . $row['genero_genero'] . "</td>";
    echo "<td>" . $row['tipo_inscripcion'] . "</td>";
    echo "<td>" . $row['estado'] . "</td>";
    echo "<td>" . $row['id_programa'] . "</td>";
    echo "</tr>";
  }

  // Cerrar la conexión a la base de datos
  $conn->close();
  ?>
</table>
</body>
</html>
