<?php
// Incluir el archivo de conexión a la base de datos
// require_once 'ConexionBD.php';
require_once '../ConexionBD.php';
// Crear una instancia de la clase DatabaseConnection
$dbConnection = new DatabaseConnection();
$conn = $dbConnection->getDBConnection();

// Resto del código ...


// URL del archivo CSV
$url = "file:///C:/xampp/htdocs/ProyectoPhp/Insert/Lista_de_Egresados_por_Proyecto.csv";

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
    $id_estudiante = $data_matrix[$i][6];
    $nombres = $data_matrix[$i][7];
    // $genero='NO REGISTRA';
    $carrera= $data_matrix[$i][2];
    $documento = $data_matrix[$i][8];
    $localidad='NO APLICA';
    // $genero_genero='NO REGISTRA';
    $tipo_inscripcion='NO APLICA';
    $estado = "ESTUDIANTE GRADUADO";
    $id_programa= $data_matrix[$i][1];
    $promedio = $data_matrix[$i][15];
    $pasantia = $data_matrix[$i][16];
    $puntaje_icfes = 0;
    $fecha_grado = $data_matrix[$i][9];
    // Calcular el semestre según el mes y el id_periodo
    $year = date('Y', strtotime($fecha_grado));
    $month = date('n', strtotime($fecha_grado));
    $semestre = ($month <= 6) ? 1 : 2;
  
    
    if ($id_programa !== "578" && $id_programa !== "678") {
      if ($carrera === "TECNOLOGIA EN SISTEMATIZACION DE DATOS (CICLOS PROPEDEUTICOS)") {
          $id_programa = "578";
      } elseif ($carrera === "INGENIERIA EN TELEMATICA (CICLOS PROPEDEUTICOS)") {
          $id_programa = "678";
      } else {
          // Si no es ninguna de las carreras válidas, continuamos con el siguiente registro
          continue;
      }
  }
   

    // Verificar si el estudiante ya existe en la tabla 'estudiante'
    $sql_check_student = "SELECT COUNT(*) FROM estudiante WHERE id_estudiante = ?";
    $stmt_check_student = $conn->prepare($sql_check_student);
    $stmt_check_student->bind_param("s", $id_estudiante);
    $stmt_check_student->execute();
    $stmt_check_student->bind_result($student_count);
    $stmt_check_student->fetch();
    $stmt_check_student->close();

    if ($student_count > 0) {
      $insertion_alert = true;
      $alerts_by_student = $alerts_by_student.", ".$id_estudiante;
   
    } else {
      // Preparar la consulta SQL para insertar el estudiante
      $sql = "INSERT INTO estudiante (id_estudiante, nombres, carrera, documento, localidad, tipo_inscripcion, estado, id_programa, promedio, pasantia, tipo_icfes,puntaje_icfes) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";

// Para obtener mes y semestre de periodo
// Escapar los valores para evitar inyecciones SQL (esto depende del tipo de base de datos que estés utilizando)
$year = mysqli_real_escape_string($conn, $year);
$semestre = mysqli_real_escape_string($conn, $semestre);
// Consultar el id_periodo que coincide con el año y el semestre
$sql_periodo = "SELECT id_periodo FROM periodo WHERE año = '$year' AND semestre = '$semestre'";
$result_periodo = $conn->query($sql_periodo);

// Verificar si se encontró el id_periodo
if ($result_periodo && $result_periodo->num_rows > 0) {
  $row = $result_periodo->fetch_assoc();
  $id_periodo = $row['id_periodo'];
} else {
  // Si no se encontró el id_periodo, se coloca por defecto 1
  $id_periodo = 1;
}

// Preparar la sentencia
$stmt = $conn->prepare($sql);

// Asignar los valores a los parámetros de la consulta
// Ejecutar la consulta
// $stmt->bind_param("issiisssidsd", $id_estudiante, $nombres, $carrera, $documento, $estrato, $localidad, $tipo_inscripcion, $estado, $id_programa, $promedio, $pasantia,$puntaje_icfes);
$stmt->bind_param("issssssidssd", $id_estudiante, $nombres, $carrera, $documento, $localidad, $tipo_inscripcion, $estado, $id_programa, $promedio, $pasantia, $tipo_icfes, $puntaje_icfes);

if (!$stmt->execute()) {
  $insertion_error= true;
  $errors_by_student = $errors_by_student.", ".$id_estudiante. $stmt->error;


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
  Los estudiantes graduados con los siguientes ID, no se pueden insertar en la tabla ESTUDIANTE:' . $errors_by_student . '<br>
  <div style="position: absolute; top: 1rem; left: 1rem; font-size: 3rem; color: rgba(255, 99, 132, 1)">❸</div>
  <div style="position: absolute; left: 50%;">
      <span style="font-size: 4rem;">&#8595;</span>
  </div>
</div>';
}else if($insertion_alert){
  echo '<div style="background-color: #FBFFBA; color: black; padding: 10px; text-align: center;border-radius: 0.8rem;
  border: 2px solid orange; width: 70rem; position: relative;margin-bottom: 2rem;">
  <span style="font-size: 2rem;color:orange">¡ALERTA!</span><br>
  Los graduados con los siguientes ID, ya existen en la tabla ESTUDIANTE. Se omitirá la inserción. ' . $alerts_by_student . ' 
  <div style="position: absolute; top: 1rem; left: 1rem; font-size: 3rem;color:orange">❸</div>
  <div style="position: absolute;  left: 50%;">
   <span style="font-size: 4rem;">&#8595;</span>
  </div>
  </div>';
}
if (!$insertion_error) {

    //echo '<span style="font-size: 24px; color: green;">✔ CARGA EXITOSA</span> Egresados insertados en la tabla ESTUDIANTE. <br>';
    echo '<div style="background-color: #efffef; color: black; padding: 10px; text-align: center;border-radius: 0.8rem;
    border: 2px solid #4CAF50; width: 70rem; position: relative;margin-bottom: 2rem;">
    <span style="font-size: 2rem;color:#4CAF50">✔ CARGA EXITOSA</span><br>
    Graduados insertados correctamente en la tabla ESTUDIANTE. 
    <div style="position: absolute; top: 1rem; left: 1rem; font-size: 3rem;color:#4CAF50">❸</div>
    <div style="position: absolute;  left: 50%;">
     <span style="font-size: 4rem;">&#8595;</span>
    </div>
    </div>';
  }
// Cerrar la conexión a la base de datos
$conn->close();
?>