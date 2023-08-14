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
    <?php include 'header.html'; ?>
    <div class="tables">
        <!-- PERIODOS ACADEMICOS -->
        <div class="subtitles"><a href="#" onclick="toggleTable('table-periodo')">Periodos
                académicos ▼</a></div>
        <div id="table-periodo" style="display: none;margin-top:1rem;margin-botoom:1rem">
            <table>
                <tr>
                    <th>id_periodo</th>
                    <th>anio</th>
                    <th>semestre</th>
                    <th>cohorte</th>
                </tr>
                <?php
// Incluir el archivo de conexión a la base de datos
include "ConexionBD.php";
// Obtener la conexión a la base de datos
$conn = getDBConnection();
// Realizar la consulta a la base de datos
  // Realizar la consulta a la base de datos para estudiantes retirados
  $queryPeriodo= "SELECT * FROM periodo";
  $resultPeriodo = mysqli_query($conn, $queryPeriodo);

  // Obtener y mostrar los datos en la tabla de estudiantes retirados
  while ($row = mysqli_fetch_assoc($resultPeriodo)) {
    echo "<tr>";
    echo "<td>" . $row['id_periodo'] . "</td>";
    echo "<td>" . $row['anio'] . "</td>";
    echo "<td>" . $row['semestre'] . "</td>";
    echo "<td>" . $row['cohorte'] . "</td>";
    echo "</tr>";
  }

  ?>
            </table>
        </div>
        <!-- PROGRAMAS ACADEMICOS -->
        <div class="subtitles"><a href="#" onclick="toggleTable('table-programa')">Programas académicos ▼</a></div>
        <div id="table-programa" style="display: none;margin-top:1rem;margin-botoom:1rem">
            <table>
                <tr>
                    <th>id_programa</th>
                    <th>nombre</th>
                </tr>
                <?php

  // Realizar la consulta a la base de datos para estudiantes retirados
  $queryPrograma= "SELECT * FROM programa";
  $resultPrograma = mysqli_query($conn, $queryPrograma);

  // Obtener y mostrar los datos en la tabla de estudiantes retirados
  while ($row = mysqli_fetch_assoc($resultPrograma)) {
    echo "<tr>";
    echo "<td>" . $row['id_programa'] . "</td>";
    echo "<td>" . $row['nombre'] . "</td>";
    echo "</tr>";
  }

  ?>
            </table>
        </div>
        <!--ESTUDIANTES-->
        <div class="subtitles"><a href="#" onclick="toggleTable('table-estudiantes')">Estudiantes ▼</a></div>
        <div id="table-estudiantes" style="display: none;margin-top:1rem;margin-botoom:1rem">
            <table>
                <tr>
                    <th>ID Estudiante</th>
                    <th>Nombres</th>
                    <th>Carrera</th>
                    <th>Documento</th>
                    <th>Estrato</th>
                    <th>Localidad</th>
                    <th>Tipo Inscripción</th>
                    <th>Estado</th>
                    <th>Promedio_grado</th>
                    <th>Pasantía</th>
                    <th>Tipo_Icfes</th>
                    <th>Icfes</th>
                </tr>
                <?php

  // Realizar la consulta a la base de datos para estudiantes retirados
  $queryEstudiante = "SELECT * FROM estudiante";
  $resultEstudiante= mysqli_query($conn, $queryEstudiante);

  // Obtener y mostrar los datos en la tabla de estudiantes retirados
  while ($row = mysqli_fetch_assoc($resultEstudiante)) {
    echo "<tr>";
    echo "<td>" . $row['id_estudiante'] . "</td>";
    echo "<td>" . $row['nombres'] . "</td>";
    echo "<td>" . $row['carrera'] . "</td>";
    echo "<td>" . $row['documento'] . "</td>";
    echo "<td>" . $row['estrato'] . "</td>";
    echo "<td>" . $row['localidad'] . "</td>";
    echo "<td>" . $row['tipo_inscripcion'] . "</td>";
    echo "<td>" . $row['estado'] . "</td>";
    echo "<td>" . $row['promedio'] . "</td>";
    echo "<td>" . $row['pasantia'] . "</td>";
    echo "<td>" . $row['tipo_icfes'] . "</td>";
    echo "<td>" . $row['puntaje_icfes'] . "</td>";
    echo "</tr>";
  }

  ?>
            </table>
        </div>
        <!-- ESTUDIANTES RETIRADOS-->
        <div class="subtitles"><a href="#" onclick="toggleTable('table-retirados')">Estudiantes retirados ▼</a></div>
        <div id="table-retirados" style="display: none;margin-top:1rem;margin-botoom:1rem">
            <table>
                <tr>
                    <th>id_retiro</th>
                    <th>id_periodo</th>
                    <th>total</th>
                </tr>
                <?php

  // Realizar la consulta a la base de datos para estudiantes retirados
  $queryRetirado = "SELECT * FROM retirado";
  $resultRetirado = mysqli_query($conn, $queryRetirado);

  // Obtener y mostrar los datos en la tabla de estudiantes retirados
  while ($row = mysqli_fetch_assoc($resultRetirado)) {
    echo "<tr>";
    echo "<td>" . $row['id_retiro'] . "</td>";
    echo "<td>" . $row['id_periodo'] . "</td>";
    echo "<td>" . $row['total'] . "</td>";
    echo "</tr>";
  }


  ?>
            </table>
        </div>
        <!-- ESTUDIANTES GRADUADOS-->
        <div class="subtitles"><a href="#" onclick="toggleTable('table-graduados')">Estudiantes graduados ▼</a></div>
        <div id="table-graduados" style="display: none;margin-top:1rem;margin-botoom:1rem">
            <table>
                <tr>
                    <th>id_graduado</th>
                    <th>id_estudiante</th>
                    <th>id_periodo</th>
                    <th>fecha_grado</th>
                    <th>promedio</th>
                </tr>
                <?php

  // Realizar la consulta a la base de datos para estudiantes retirados
  $queryGraduado= "SELECT * FROM graduado";
  $resultGraduado = mysqli_query($conn, $queryGraduado);

  // Obtener y mostrar los datos en la tabla de estudiantes retirados
  while ($row = mysqli_fetch_assoc($resultGraduado)) {
    echo "<tr>";
    echo "<td>" . $row['id_graduado'] . "</td>";
    echo "<td>" . $row['id_estudiante'] . "</td>";
    echo "<td>" . $row['id_periodo'] . "</td>";
    echo "<td>" . $row['fecha_grado'] . "</td>";
    echo "<td>" . $row['promedio'] . "</td>";
    echo "</tr>";
  }

  ?>
            </table>
        </div>


        <!-- ESTUDIANTES PRIMIPAROS-->
        <div class="subtitles"><a href="#" onclick="toggleTable('table-primiparos')">Estudiantes primiparos ▼</a></div>
        <div id="table-primiparos" style="display: none;margin-top:1rem;margin-botoom:1rem">
            <table>
                <tr>
                    <th>id_primiparo</th>
                    <th>id_estudiante</th>
                    <th>id_periodo</th>
                </tr>
                <?php

  // Realizar la consulta a la base de datos para estudiantes retirados
  $queryPrimiparo= "SELECT * FROM primiparo";
  $resultPrimiparo = mysqli_query($conn, $queryPrimiparo);

  // Obtener y mostrar los datos en la tabla de estudiantes retirados
  while ($row = mysqli_fetch_assoc($resultPrimiparo)) {
    echo "<tr>";
    echo "<td>" . $row['id_primiparo'] . "</td>";
    echo "<td>" . $row['id_estudiante'] . "</td>";
    echo "<td>" . $row['id_periodo'] . "</td>";
    echo "</tr>";
  }

  ?>
            </table>
        </div>
        <!-- ESTUDIANTES ADMITIDOS-->
        <!-- <div class="subtitles"><a href="#" onclick="toggleTable('table-graduados')">Estudiantes admitidos ▼</a></div>
        <div id="table-graduados" style="display: none;margin-top:1rem;margin-botoom:1rem">
            <table>
                <tr>
                    <th>id_graduado</th>
                    <th>id_estudiante</th>
                    <th>id_periodo</th>
                    <th>fecha_grado</th>
                    <th>promedio</th>
                </tr>
                <?php

  // Realizar la consulta a la base de datos para estudiantes retirados
//   $queryGraduado= "SELECT * FROM graduado";
//   $resultGraduado = mysqli_query($conn, $queryGraduado);

  // Obtener y mostrar los datos en la tabla de estudiantes retirados
//   while ($row = mysqli_fetch_assoc($resultGraduado)) {
//     echo "<tr>";
//     echo "<td>" . $row['id_graduado'] . "</td>";
//     echo "<td>" . $row['id_estudiante'] . "</td>";
//     echo "<td>" . $row['id_periodo'] . "</td>";
//     echo "<td>" . $row['fecha_grado'] . "</td>";
//     echo "<td>" . $row['promedio'] . "</td>";
//     echo "</tr>";
//   }

  ?>
            </table>
        </div> -->

        <!-- ESTUDIANTES MATRICULADOS-->
        <div class="subtitles"><a href="#" onclick="toggleTable('table-matriculados')">Estudiantes matriculados ▼</a>
        </div>
        <div id="table-matriculados" style="display: none;margin-top:1rem;margin-botoom:1rem">
            <table>
                <tr>
                    <th>id_matricula</th>
                    <th>id_estudiante</th>
                    <th>id_periodo</th>
                    <th>estado_matricula</th>
                </tr>
                <?php

  // Realizar la consulta a la base de datos para estudiantes retirados
  $queryMatriculado= "SELECT * FROM matriculado";
  $resultMatriculado = mysqli_query($conn, $queryMatriculado);

  // Obtener y mostrar los datos en la tabla de estudiantes retirados
  while ($row = mysqli_fetch_assoc($resultMatriculado)) {
    echo "<tr>";
    echo "<td>" . $row['id_matricula'] . "</td>";
    echo "<td>" . $row['id_estudiante'] . "</td>";
    echo "<td>" . $row['id_periodo'] . "</td>";
    echo "<td>" . $row['estado_matricula'] . "</td>";
    echo "</tr>";
  }

  // Cerrar la conexión a la base de datos
  $conn->close();
  ?>
            </table>
        </div>

    </div>

    </div>
    <button class="arrow-button" onclick="goBack()">&#8592;</button>
    <script>
    function toggleTable(tableId) {
        var table = document.getElementById(tableId);
        if (table.style.display === "none") {
            table.style.display = "table";
        } else {
            table.style.display = "none";
        }
    }
    </script>
    <script>
    function goBack() {
        // window.history.back();
        window.location.href = 'http://localhost/ProyectoPhp/Home.php#';
    }
    </script>
</body>

</html>