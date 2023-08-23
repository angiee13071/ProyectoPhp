<?php
// Incluir el archivo de conexión a la base de datos
// require_once 'ConexionBD.php';
require_once '../ConexionBD.php';
// Crear una instancia de la clase DatabaseConnection
$dbConnection = new DatabaseConnection();
$conn = $dbConnection->getDBConnection();

// URL del archivo CSV
$url = "file:///C:/xampp/htdocs/ProyectoPhp/Insert/Listado_matriculados_período_actual.csv";

// Obtener la conexión a la base de datos
// $conn = getDBConnection();

// Obtener el contenido del archivo en un array
$file_data = file($url, FILE_IGNORE_NEW_LINES);

// Crear una matriz para almacenar los datos
$data_matrix = [];

// Iterar sobre cada línea del archivo
foreach ($file_data as $line) {
    // Verificar si la línea contiene contenido antes de procesarla
    if (!empty($line)) {
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
   
}

// Variable para controlar si hubo algún error durante el proceso de inserción
$errors_by_student = "";
$alerts_by_student = "";
$insertion_error = false;
$insertion_alert = false;


// Insertar los datos en la tabla 'matriculado'
for ($i = 2; $i < count($data_matrix); $i++) {
    $id_matricula = $i-1;
    $id_estudiante = isset($data_matrix[$i][5]) ? $data_matrix[$i][5] : null;
    if (!empty($id_estudiante) && isset($data_matrix[$i][1]) && isset($data_matrix[$i][12])) {
        $periodo = $data_matrix[$i][1];
        // echo "ID Matrícula: $id_matricula<br>";
        // echo "ID Estudiante: $id_estudiante<br>";
        // echo "Periodo: $periodo<br>";
        // Extraer el año y el semestre del periodo
        $year_semester = explode('-', $periodo);
        $anio = $year_semester[0];
        $semestre = $year_semester[1];
    
        // Buscar el id_periodo en la tabla 'periodo' utilizando el año y el semestre
        $sql_get_id_periodo = "SELECT id_periodo FROM periodo WHERE anio = ? AND semestre = ?";
        $stmt_get_id_periodo = $conn->prepare($sql_get_id_periodo);
        $stmt_get_id_periodo->bind_param("ii", $anio, $semestre);
        $stmt_get_id_periodo->execute();
        $stmt_get_id_periodo->bind_result($id_periodo);
        $stmt_get_id_periodo->fetch();
        $stmt_get_id_periodo->close();
        
        $estado_matricula = $data_matrix[$i][12];
    }
    

    // Verificar si el registro ya existe en la base de datos
    $sql_check_existing = "SELECT COUNT(*) FROM estudiante WHERE id_estudiante = ?";
    $stmt_check_existing = $conn->prepare($sql_check_existing);
    $stmt_check_existing->bind_param("s", $id_estudiante);
    $stmt_check_existing->execute();
    $stmt_check_existing->bind_result($existing_count);
    $stmt_check_existing->fetch();
    $stmt_check_existing->close();
    //Si hay datos vacios no los subimos
    if($id_estudiante!=null||$id_estudiante!=''){
        if ($existing_count > 0) {
         
            $sql_insert = "INSERT INTO matriculado (id_matricula, id_estudiante, id_periodo, estado_matricula)
                VALUES (?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("iiis", $id_matricula, $id_estudiante, $id_periodo, $estado_matricula);

            if (!$stmt_insert->execute()) {
                $insertion_error= true;
//   $errors_by_student = $errors_by_student.", ".$id_estudiante. $stmt_insert->error;
$errors_by_student = $errors_by_student.", ".$id_estudiante;
            } else {
                $insertion_error = false;
            }
    
            // Incrementar el valor de $id_matricula para el siguiente registro
            $id_matricula++;
    
            // Cerrar la sentencia
            $stmt_insert->close();
        } else {
            $insertion_alert = true;
            $alerts_by_student = $alerts_by_student.", ".$id_estudiante. $stmt_insert->error;
        }
    }
        
    // }
   
}


// Cerrar la conexión a la base de datos después de haber procesado todos los datos
if (!$insertion_error) {
    //echo '<span style="font-size: 24px; color: green;">✔ CARGA EXITOSA</span> Estudiantes matriculados en el periodo actual insertados en la tabla MATRICULADO. <br>';
    echo '<div style="background-color: #efffef; color: black; padding: 10px; text-align: center;border-radius: 0.8rem;
    border: 2px solid #4CAF50; width: 70rem; position: relative;margin-bottom: 2rem;">
    <span style="font-size: 2rem;color:#4CAF50">✔ CARGA EXITOSA</span><br>
    Estudiantes matriculados insertados correctamente en la tabla MATRICULADO.
    <div style="position: absolute; top: 1rem; left: 1rem; font-size: 3rem;color:#4CAF50">❹</div>
    <div style="position: absolute;  left: 50%;">
     <span style="font-size: 4rem;">&#8595;</span>
    </div>
    </div>';
}

if($insertion_error){
    echo '<div style="background-color: #FFE1E1; color: black; padding: 10px; text-align: center;border-radius: 0.8rem;
                border: 2px solid rgba(255, 99, 132, 1); width: 70rem; position: relative;margin-bottom: 2rem;">
                <span style="font-size: 2rem;color:rgba(255, 99, 132, 1)">X ERROR</span><br>
                Los estudiantes matriculados con los siguientes ID, no se pueden insertar en la tabla MATRICULADO porqué la tabla ya cuenta con el id primario' .$errors_by_student. ' "., "<br>";
                <div style="position: absolute; top: 1rem; left: 1rem; font-size: 3rem;color:rgba(255, 99, 132, 1)">❹</div>
                <div style="position: absolute;  left: 50%;">
                 <span style="font-size: 4rem;">&#8595;</span>
                </div>
                </div>'; 
}else if($insertion_alert){
    echo '<div style="background-color: #FBFFBA; color: black; padding: 10px; text-align: center;border-radius: 0.8rem;
    border: 2px solid orange; width: 70rem; position: relative;margin-bottom: 2rem;">
    <span style="font-size: 2rem;color:orange">¡ALERTA!</span><br>
    Los estudiantes matriculados con ID, no existen en la tabla MATRICULADO o son de otra carrera. Se omitirá la inserción en la tabla MATRICULADO.'.$alerts_by_student.' <br>";
    <div style="position: absolute; top: 1rem; left: 1rem; font-size: 3rem;color:orange">❹</div>
    <div style="position: absolute;  left: 50%;">
     <span style="font-size: 4rem;">&#8595;</span>
    </div>
    </div>'; 
}
// Cerrar la conexión a la base de datos
$conn->close();
?>