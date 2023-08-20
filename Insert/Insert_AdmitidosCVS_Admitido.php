<?php
// Incluir el archivo de conexión a la base de datos
// require_once 'ConexionBD.php';
require_once '../ConexionBD.php';
// Crear una instancia de la clase DatabaseConnection
$dbConnection = new DatabaseConnection();
$conn = $dbConnection->getDBConnection();
// URL del archivo CSV
$url = "file:///C:/xampp/htdocs/ProyectoPhp/Insert/Listado_de_admitidos.csv";

// Obtener el contenido del archivo en un array
$file_data = file($url, FILE_IGNORE_NEW_LINES);

// Crear una matriz para almacenar los datos
$data_matrix = [];

// Iterar sobre cada línea del archivo
foreach ($file_data as $line) {
    // Dividir la línea en sus elementos utilizando la coma como separador
    $row = explode(",", $line);

    // Eliminar las comillas de cada dato
    $row = array_map('trim', $row);
    $row = array_map(function($item) {
        return str_replace('"', '', $item);
    }, $row);

    // Agregar la fila a la matriz de datos
    $data_matrix[] = $row;
}

// // Obtener la conexión a la base de datos
// $conn = getDBConnection();

// Variable para controlar si hubo algún error durante el proceso de inserción
$errors_by_student = "";
$alerts_by_student = "";
$insertion_error = false;
$insertion_alert = false;

// Insertar los datos en la tabla 'graduado'
for ($i = 2; $i < count($data_matrix); $i++) {
  $id_admitido = $i-1;
  $id_estudiante = $data_matrix[$i][5];
  $periodo = $data_matrix[$i][4];
  $tipo_inscripcion=$data_matrix[$i][10];
  $tipo_icfes= $data_matrix[$i][11];
  $puntaje_icfes = $data_matrix[$i][12];

  // Calcular el semestre según el mes y el id_periodo
  $year = date('Y', strtotime($periodo));
  $semestre = date('n', strtotime($periodo));
  
  //Identificar el Tipo de ICFES
    if ($tipo_icfes) {
      if ($tipo_icfes === "A") {
          $tipo_icfes = "ICFES Saber 11 ";
      } elseif ($tipo_icfes === "V") {
        $tipo_icfes= "ICFES Saber Pro (antes ECAES)";
      }   elseif ($tipo_icfes === "N") {
        $tipo_icfes = "ICFES Validación del Bachillerato";
    } 
      else {
        $tipo_icfes = "NO REGISTRA";
          continue;
      }
  }

  // Verificar si el estudiante ya existe en la tabla 'graduado'
  $sql_check_existing = "SELECT COUNT(*) FROM admitido WHERE id_estudiante = ?";
  $stmt_check_existing = $conn->prepare($sql_check_existing);
  $stmt_check_existing->bind_param("s", $id_estudiante);
  $stmt_check_existing->execute();
  $stmt_check_existing->bind_result($existing_count);
  $stmt_check_existing->fetch();
  $stmt_check_existing->close();

  if ($existing_count > 0) {
      $insertion_alert = true;
      $alerts_by_student = $alerts_by_student.", ".$id_estudiante;
  
  } else {
    $sql_periodo = "SELECT id_periodo FROM periodo WHERE anio = ? AND semestre = ?";
    $stmt_periodo = $conn->prepare($sql_periodo);
          if ($stmt_periodo) {
            $stmt_periodo->bind_param("ii", $year, $semestre);
            $stmt_periodo->execute();
            $stmt_periodo->bind_result($id_periodo);
            $stmt_periodo->fetch();
            $stmt_periodo->close();
          } else {
            echo "Error en la consulta preparada para obtener id_periodo: " . $conn->error;
            exit;
          }
          // Preparar la consulta SQL
          $sql = "INSERT INTO admitido (id_admitido, id_estudiante, id_periodo, tipo_inscripcion, tipo_icfes, puntaje_icfes) 
          VALUES (?, ?, ?, ?, ?, ?)";
          
          // Preparar la sentencia
          $stmt = $conn->prepare($sql);
          
          // Asignar los valores a los parámetros de la consulta
          // Ejecutar la consulta
          $stmt->bind_param("iiissd", $id_admitido, $id_estudiante, $id_periodo, $tipo_inscripcion, $tipo_icfes, $puntaje_icfes);
          
            if (!$stmt->execute()) {
              $insertion_error= true;
              $errors_by_student = $errors_by_student.", ".$id_estudiante.$stmt->error;
            } else {
            $insertion_error = false;
          }
          // Cerrar la sentencia
          $stmt->close();
        }
      }

// Mostrar mensaje de éxito si no se encontraron estudiantes duplicados
if($insertion_error){
     echo '<div style="background-color: #FFE1E1; color: black; padding: 10px; text-align: center; border-radius: 0.8rem;
  border: 2px solid rgba(255, 99, 132, 1); width: 70rem; position: relative; margin-bottom: 2rem;">
  <span style="font-size: 2rem; color: rgba(255, 99, 132, 1)">X ERROR</span><br>
  Los estudiantes admitidos con los siguientes ID, no se pueden no se pueden insertar en la tabla ADMITIDOS.' . $errors_by_student . '<br>
  <div style="position: absolute; top: 1rem; left: 1rem; font-size: 3rem; color: rgba(255, 99, 132, 1)">❾</div>
  <div style="position: absolute; left: 50%;">
      <span style="font-size: 4rem;">&#8595;</span>
  </div>
</div>';
}else if($insertion_alert){
      echo '<div style="background-color: #FBFFBA; color: black; padding: 10px; text-align: center; border-radius: 0.8rem;
              border: 2px solid orange; width: 70rem; position: relative; margin-bottom: 2rem;">
              <span style="font-size: 2rem; color: orange">¡ALERTA!</span><br>
              Los estudiantes admitidos con los siguientes ID, ya existen en la tabla ADMITIDOS. Se omitirá la inserción: ' . $alerts_by_student . '
              <div style="position: absolute; top: 1rem; left: 1rem; font-size: 3rem; color: orange">❾</div>
              <div style="position: absolute; left: 50%;">
                  <span style="font-size: 4rem;">&#8595;</span>
              </div>
         </div>';
}
else if (!$insertion_error) {
    //echo '<span style="font-size: 24px; color: green;">✔ CARGA EXITOSA</span> Admitidos insertados en la tabla ESTUDIANTE. <br>';
    echo '<div style="background-color: #efffef; color: black; padding: 10px; text-align: center;border-radius: 0.8rem;
    border: 2px solid #4CAF50; width: 70rem; position: relative;margin-bottom: 2rem;">
    <span style="font-size: 2rem;color:#4CAF50">✔ CARGA EXITOSA</span><br>
    Admitidos insertados correctamente en la tabla ADMITIDOS. 
    <div style="position: absolute; top: 1rem; left: 1rem; font-size: 3rem;color:#4CAF50">⑩</div>
    <div style="position: absolute;  left: 50%;">
     <span style="font-size: 4rem;">&#8595;</span>
    </div>
    </div>';
  }
// Cerrar la conexión a la base de datos
$conn->close();
