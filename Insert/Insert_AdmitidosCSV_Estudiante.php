<?php
// Incluir el archivo de conexión a la base de datos
// require_once 'ConexionBD.php';
require_once '../ConexionBD.php';

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

// Obtener la conexión a la base de datos
$conn = getDBConnection();

// Variable para controlar si hubo algún error durante el proceso de inserción
$insertion_error = false;

// Insertar los datos en la tabla 'graduado'
for ($i = 2; $i < count($data_matrix); $i++) {
    $id_estudiante = $data_matrix[$i][5];
    $nombres = $data_matrix[$i][8] . " " . $data_matrix[$i][7];
    $genero='NO REGISTRA';
    $carrera= $data_matrix[$i][1];
    $documento = $data_matrix[$i][6];
    $estrato=$data_matrix[$i][13]; 
    $localidad='NO APLICA';
    $genero_genero='NO REGISTRA';
    $tipo_inscripcion=$data_matrix[$i][10];
    $estado = "ESTUDIANTE ADMITIDO";
    $id_programa= $data_matrix[$i][0];
    $promedio = 'NO APLICA';
    $pasantia = 'NO APLICA';
    $puntaje_icfes = $data_matrix[$i][12];
    $periodo = $data_matrix[$i][4];
    // Calcular el semestre según el mes y el id_periodo
    $year = date('Y', strtotime($periodo));
    $month = date('n', strtotime($periodo));
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
        $insertion_error = true;
        // El estudiante ya existe en la tabla 'estudiante', proceder con la inserción en 'graduado'
              // echo "<span style='font-size: 24px; color: orange;'>¡ALERTA!</span> El egresado con ID $id_estudiante ya existe en la tabla ESTUDIANTE. Se omitirá la inserción.<br>";
              echo '<div style="background-color: #FBFFBA; color: black; padding: 10px; text-align: center;border-radius: 0.8rem;
              border: 2px solid orange; width: 70rem; position: relative;margin-bottom: 2rem;">
              <span style="font-size: 2rem;color:orange">¡ALERTA!</span><br>
              El egresado con ID $id_estudiante ya existe en la tabla ESTUDIANTE. Se omitirá la inserción.
              <div style="position: absolute; top: 1rem; left: 1rem; font-size: 3rem;color:orange">❽</div>
              <div style="position: absolute;  left: 50%;">
               <span style="font-size: 4rem;">&#8595;</span>
              </div>
              </div>';    
       
    } else {
      // Preparar la consulta SQL para insertar el estudiante
      $sql = "INSERT INTO estudiante (id_estudiante, nombres, genero, carrera, documento, estrato, localidad, genero_genero, tipo_inscripcion, estado, id_programa, promedio, pasantia, puntaje_icfes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

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
$stmt->bind_param("isssiissssidsd", $id_estudiante, $nombres, $genero, $carrera, $documento, $estrato, $localidad, $genero_genero, $tipo_inscripcion, $estado, $id_programa, $promedio, $pasantia,$puntaje_icfes);

if (!$stmt->execute()) {
  $insertion_error = true;
  //echo "<span style='font-size: 24px; color: red;'>X ERROR</span> El admitido con ID $id_estudiante no se pudo insertar en la tabla ESTUDIANTE: ". $stmt->error ,"<br>";
 echo '<div style="background-color: #FFE1E1; color: black; padding: 10px; text-align: center;border-radius: 0.8rem;
        border: 2px solid rgba(255, 99, 132, 1); width: 70rem; position: relative;margin-bottom: 2rem;">
        <span style="font-size: 2rem;color:rgba(255, 99, 132, 1)">X ERROR</span><br>
        El admitido con ID $id_estudiante no se pudo insertar en la tabla ESTUDIANTE: ". $stmt->error ,"<br>";
        <div style="position: absolute; top: 1rem; left: 1rem; font-size: 3rem;color:rgba(255, 99, 132, 1)">❽</div>
        <div style="position: absolute;  left: 50%;">
         <span style="font-size: 4rem;">&#8595;</span>
        </div>
        </div>'; 
} else {
  $insertion_error = false;
}

// Cerrar la sentencia
$stmt->close();
    }
   
}

// Cerrar la conexión a la base de datos
$conn->close();

// Mostrar mensaje de éxito si no se encontraron estudiantes duplicados
if (!$insertion_error) {
    //echo '<span style="font-size: 24px; color: green;">✔ CARGA EXITOSA</span> Admitidos insertados en la tabla ESTUDIANTE. <br>';
    echo '<div style="background-color: #efffef; color: black; padding: 10px; text-align: center;border-radius: 0.8rem;
    border: 2px solid #4CAF50; width: 70rem; position: relative;margin-bottom: 2rem;">
    <span style="font-size: 2rem;color:#4CAF50">✔ CARGA EXITOSA</span><br>
    Admitidos insertados correctamente en la tabla ESTUDIANTE. 
    <div style="position: absolute; top: 1rem; left: 1rem; font-size: 3rem;color:#4CAF50">❽</div>
    <div style="position: absolute;  left: 50%;">
     <span style="font-size: 4rem;">&#8595;</span>
    </div>
    </div>';
  }

?>