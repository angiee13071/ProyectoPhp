<?php
// Incluir el archivo de conexión a la base de datos
require_once 'conexion.php';

// URL del archivo CSV
$url = "file:///C:/xampp/htdocs/ProyectoPhp/Insert/Listado_matriculados_período_actual.csv";

// Obtener la conexión a la base de datos
$conn = getDBConnection();

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
$insertion_error = false;

// Insertar los datos en la tabla 'matriculado'
$id_matricula = 1; // Variable para asignar un valor único a cada registro
for ($i = 2; $i < count($data_matrix); $i++) {
    $id_estudiante = $data_matrix[$i][5];
    $id_periodo = 1;
    $estado_matricula = $data_matrix[$i][12];

    // Verificar si el registro ya existe en la base de datos
    $sql_check_existing = "SELECT COUNT(*) FROM matriculado WHERE id_estudiante = ? AND id_periodo = ?";
    $stmt_check_existing = $conn->prepare($sql_check_existing);
    $stmt_check_existing->bind_param("ii", $id_estudiante, $id_periodo);
    $stmt_check_existing->execute();
    $stmt_check_existing->bind_result($existing_count);
    $stmt_check_existing->fetch();
    $stmt_check_existing->close();

    if ($existing_count > 0) {
        // El registro ya existe en la base de datos, mostrar una alerta o hacer otra acción si lo deseas
        echo "<span style='font-size: 24px; color: orange;'>¡ALERTA!</span> El estudiante matriculado actualmente con ID $id_estudiante ya existe en la tabla MATRICULADO. Se omitirá la inserción.<br>"; 

        $insertion_error = true;
    } else {
        // Preparar la consulta SQL para insertar el registro en 'matriculado'
        $sql_insert = "INSERT INTO matriculado (id_matricula, id_estudiante, id_periodo, estado_matricula)
            VALUES (?, ?, ?, ?)";

        // Preparar la sentencia
        $stmt_insert = $conn->prepare($sql_insert);

        // Asignar los valores a los parámetros
        $stmt_insert->bind_param("iiis", $id_matricula, $id_estudiante, $id_periodo, $estado_matricula);

        // Ejecutar la consulta
        if (!$stmt_insert->execute()) {
            // Hubo un error durante la inserción
            $insertion_error =true;
            echo "<span style='font-size: 24px; color: red;'>X ERROR</span> El estudiante con ID $id_estudiante no se pudo insertar en la tabla MATRICULADO: ". $stmt_insert->error ,"<br>";
        }else{
            $insertion_error =false;
        }

        // Incrementar el valor de $id_matricula para el siguiente registro
        $id_matricula++;

        // Cerrar la sentencia
        $stmt_insert->close();
    }
}

// Cerrar la conexión a la base de datos después de haber procesado todos los datos
$conn->close();

if (!$insertion_error) {
    echo '<span style="font-size: 24px; color: green;">✔ CARGA EXITOSA</span> Estudiantes matriculados en el periodo actual insertados en la tabla MATRICULADOS. <br>';

}
?>