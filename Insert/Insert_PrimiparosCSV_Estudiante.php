<?php
set_time_limit(300);
// Incluir el archivo de conexión a la base de datos
// require_once 'ConexionBD.php';
require_once '../ConexionBD.php';
// Crear una instancia de la clase DatabaseConnection
$dbConnection = new DatabaseConnection();
$conn = $dbConnection->getDBConnection();
// URL del archivo CSV
$url = "file:///C:/xampp/htdocs/ProyectoPhp/Insert/Listado_matriculados_a_primer_semestre.csv";

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
    $row = array_map(function ($item) {
        return str_replace('"', '', $item);
    }, $row);

    // Agregar la fila a la matriz de datos
    $data_matrix[] = $row;
}

// Variable para controlar si hubo algún error durante el proceso de inserción
$errors_by_student = "";
$alerts_by_student = "";
$insertion_error = false;
$insertion_alert = false;
// // Abrir la conexión a la base de datos
// $conn = getDBConnection();

for ($i = 2; $i < count($data_matrix); $i++) {
    $id_estudiante = $data_matrix[$i][4];
    $nombres = $data_matrix[$i][5];
    $carrera = $data_matrix[$i][1];
    $documento = $data_matrix[$i][6];
    // $estrato = null;
    // $localidad = null; // Se obtiene el valor de la columna "Localidad"
    $tipo_inscripcion = $data_matrix[$i][8];
    $estado = $data_matrix[$i][12]; // Se obtiene el valor de la columna "Estado"
    $id_programa = $data_matrix[$i][0];
    // $genero = null; // Define la variable $genero

    // Si el valor de $id_programa no es "578" ni "678", lo modificamos según la carrera
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

    //DESCOMENTAR API
    // $ultimo_nombre = array_slice(explode(" ", $nombres), -1)[0];
    // $url = "https://api.genderize.io/?name=" . urlencode($ultimo_nombre);
    // $response = file_get_contents($url);
    // $data = json_decode($response, true);
    // $genero = isset($data['gender']) ? $data['gender'] : null;

    // Verificar si el estudiante ya existe en la base de datos
    $sql_check_existing = "SELECT COUNT(*) FROM estudiante WHERE id_estudiante = ?";
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
        // Preparar la consulta SQL para insertar el estudiante
        $sql_insert = "INSERT INTO estudiante (id_estudiante, nombres, carrera, documento, estrato, localidad, tipo_inscripcion, estado, id_programa) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // Preparar la sentencia
        $stmt_insert = $conn->prepare($sql_insert);

        // Asignar los valores a los parámetros
        // Ejecutar la consulta
        $stmt_insert->bind_param("isssisssi", $id_estudiante, $nombres, $carrera, $documento, $estrato, $localidad, $tipo_inscripcion, $detalle_estado, $id_programa); // Se asigna $estado como valor del campo "estado"
        if (!$stmt_insert->execute()) {
            $insertion_error= true;
            $errors_by_student = $errors_by_student.", ".$id_estudiante. $stmt_insert->error;
        
        } else {
            $insertion_error = false;
        }
        // Cerrar la sentencia (no es necesario cerrar la conexión en este punto)
        $stmt_insert->close();
    }
}

if($insertion_alert){

    echo '<div style="background-color: #FBFFBA; color: black; padding: 10px; text-align: center;border-radius: 0.8rem;
        border: 2px solid orange; width: 70rem; position: relative;margin-bottom: 2rem;">
        <span style="font-size: 2rem;color:orange">¡ALERTA!</span><br>
        Los estudiantes nuevos con los siguientes ID, ya existen en la tabla ESTUDIANTE. Se omitirá la inserción. '.$alerts_by_student. ' 
        <div style="position: absolute; top: 1rem; left: 1rem; font-size: 3rem;color:orange">⑥</div>
        <div style="position: absolute;  left: 50%;">
          <span style="font-size: 4rem;">&#8595;</span>
        </div>
  </div>';
  
}else if($insertion_error){
    echo '<div style="background-color: #FFE1E1; color: black; padding: 10px; text-align: center;border-radius: 0.8rem;
    border: 2px solid rgba(255, 99, 132, 1); width: 70rem; position: relative;margin-bottom: 2rem;">
    <span style="font-size: 2rem;color:rgba(255, 99, 132, 1)">X ERROR</span><br>
    Los estudiantes nuevos con los siguientes  ID, no se pueden insertar en la tabla ESTUDIANTE: " '.$errors_by_student.' "<br>";
    <div style="position: absolute; top: 1rem; left: 1rem; font-size: 3rem;color:rgba(255, 99, 132, 1)">⑥</div>
    <div style="position: absolute;  left: 50%;">
     <span style="font-size: 4rem;">&#8595;</span>
    </div>
    </div>'; 
        
}
else if (!$insertion_error) {
    //echo '<span style="font-size: 24px; color: green;">✔ CARGA EXITOSA</span> Datos de estudiantes primipaross insertados en la tabla ESTUDIANTE.  <br>';
    echo '<div style="background-color: #efffef; color: black; padding: 10px; text-align: center;border-radius: 0.8rem;
    border: 2px solid #4CAF50; width: 70rem; position: relative;margin-bottom: 2rem;">
    <span style="font-size: 2rem;color:#4CAF50">✔ CARGA EXITOSA</span><br>
    Estudiantes primiparos insertados correctamente en la tabla ESTUDIANTE.
    <div style="position: absolute; top: 1rem; left: 1rem; font-size: 3rem;color:#4CAF50">⑥</div>
    <div style="position: absolute;  left: 50%;">
     <span style="font-size: 4rem;">&#8595;</span>
    </div>
    </div>';
}
// Cerrar la conexión a la base de datos
$conn->close();
