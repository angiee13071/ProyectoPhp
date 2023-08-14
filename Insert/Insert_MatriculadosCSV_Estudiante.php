<?php
set_time_limit(300);

// Incluir el archivo de conexión a la base de datos
// require_once 'ConexionBD.php';
require_once '../ConexionBD.php';

// URL del archivo CSV
$url = "file:///C:/xampp/htdocs/ProyectoPhp/Insert/Listado_matriculados_período_actual.csv";

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

// Abrir la conexión a la base de datos
$conn = getDBConnection();

// Insertar los datos en la tabla 'estudiante'
for ($i = 2; $i < count($data_matrix); $i++) {
    $id_estudiante = $data_matrix[$i][5];
    $nombres = $data_matrix[$i][6];
    $carrera = $data_matrix[$i][3];
    $documento = $data_matrix[$i][7];
    $id_programa = $data_matrix[$i][2];
    $estrato = $data_matrix[$i][15];
    $tipo_inscripcion = $data_matrix[$i][10];
    $estado = $data_matrix[$i][12]; // Se obtiene el valor de la columna "Estado"
    $localidad = $data_matrix[$i][16]; // Se obtiene el valor de la columna "Localidad"

    if ($id_programa !== "678" && $id_programa !== "578") {
        // Si no existe ninguno de los dos programas académicos, se omite la inserción del estudiante
        $insertion_error = true;
        continue;
    }

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
//         echo '<div style="background-color: #FBFFBA; color: black; padding: 10px; text-align: center;border-radius: 0.8rem;
//         border: 2px solid orange; width: 70rem; position: relative;margin-bottom: 2rem;">
//         <span style="font-size: 2rem;color:orange">¡ALERTA!</span><br>
//         El estudiante matriculado actualmente con ID ' . $id_estudiante . ' ya existe en la tabla ESTUDIANTE. Se omitirá la inserción.
//         <div style="position: absolute; top: 1rem; left: 1rem; font-size: 3rem;color:orange">③</div>
//         <div style="position: absolute;  left: 50%;">
//           <span style="font-size: 4rem;">&#8595;</span>
//         </div>
//   </div>';
  
    } else {
        // Preparar la consulta SQL para insertar el estudiante
        $sql_insert = "INSERT INTO estudiante (id_estudiante, nombres, carrera, documento, estrato, localidad, tipo_inscripcion, estado, id_programa) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // Preparar la sentencia
        $stmt_insert = $conn->prepare($sql_insert);

        // Asignar los valores a los parámetros
        $stmt_insert->bind_param("isssisssi", $id_estudiante, $nombres, $carrera, $documento, $estrato, $localidad, $tipo_inscripcion, $estado, $id_programa);

        // Ejecutar la consulta
        if (!$stmt_insert->execute()) {
            $insertion_error= true;
            $errors_by_student = $errors_by_student.", ".$id_estudiante; 
        } else {
            $insertion_error = false;
        }
        // Cerrar la sentencia (no es necesario cerrar la conexión en este punto)
        $stmt_insert->close();
    }
}

// Cerrar la conexión a la base de datos después de haber procesado todos los datos
$conn->close();
if($insertion_error){

}else if($insertion_alert){
    echo '<div style="background-color: #FFE1E1; color: black; padding: 10px; text-align: center;border-radius: 0.8rem;
    border: 2px solid rgba(255, 99, 132, 1); width: 70rem; position: relative;margin-bottom: 2rem;">
    <span style="font-size: 2rem;color:rgba(255, 99, 132, 1)">X ERROR</span><br>
    Los estudiantes matriculados con ID: ' . $errors_by_student . ' no se pueden insertar en la tabla ESTUDIANTE: ' . $stmt_insert->error . '<br>
    <div style="position: absolute; top: 1rem; left: 1rem; font-size: 3rem;color:rgba(255, 99, 132, 1)">➌</div>
    <div style="position: absolute;  left: 50%;">
      <span style="font-size: 4rem;">&#8595;</span>
    </div>
</div>';
}
else if (!$insertion_error) {
    //echo '<span style="font-size: 24px; color: green;">✔ CARGA EXITOSA</span> Estudiantes matriculados en el periodo actual insertados en la tabla ESTUDIANTE. <br>';
    echo '<div style="background-color: #efffef; color: black; padding: 10px; text-align: center;border-radius: 0.8rem;
    border: 2px solid #4CAF50; width: 70rem; position: relative;margin-bottom: 2rem;">
    <span style="font-size: 2rem;color:#4CAF50">✔ CARGA EXITOSA</span><br>
    Estudiantes matriculados insertados correctamente en la tabla ESTUDIANTE.
    <div style="position: absolute; top: 1rem; left: 1rem; font-size: 3rem;color:#4CAF50">➌</div>
    <div style="position: absolute;  left: 50%;">
     <span style="font-size: 4rem;">&#8595;</span>
    </div>
    </div>';
}
?>