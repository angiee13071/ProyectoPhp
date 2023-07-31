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
for ($i = 2; $i < count($data_matrix); $i++) {
    $id_matricula = $i-1;
    $id_estudiante = $data_matrix[$i][5];
    $periodo = $data_matrix[$i][1];
    
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

    // Verificar si el registro ya existe en la base de datos
    $sql_check_existing = "SELECT COUNT(*) FROM estudiante WHERE id_estudiante = ?";
    $stmt_check_existing = $conn->prepare($sql_check_existing);
    $stmt_check_existing->bind_param("s", $id_estudiante);
    $stmt_check_existing->execute();
    $stmt_check_existing->bind_result($existing_count);
    $stmt_check_existing->fetch();
    $stmt_check_existing->close();

    if ($existing_count > 0) {
        // El estudiante ya existe en la tabla 'estudiante', proceder con la inserción en la tabla 'matriculado'
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
            $insertion_error = true;
            echo "<span style='font-size: 24px; color: red;'>X ERROR</span> El estudiante con ID $id_estudiante no se pudo insertar en la tabla MATRICULADO: " . $stmt_insert->error, "<br>";
        } else {
            $insertion_error = false;
        }

        // Incrementar el valor de $id_matricula para el siguiente registro
        $id_matricula++;

        // Cerrar la sentencia
        $stmt_insert->close();
    } else {
        // El estudiante no existe en la tabla 'estudiante', mostrar una alerta o hacer otra acción si lo deseas
        echo "<span style='font-size: 24px; color: orange;'>¡ALERTA!</span> El estudiante con ID $id_estudiante no existe en la tabla ESTUDIANTE. Se omitirá la inserción en la tabla MATRICULADO.<br>";

        //$insertion_error = true;
    }
}

// Cerrar la conexión a la base de datos después de haber procesado todos los datos
$conn->close();

if (!$insertion_error) {
    echo '<span style="font-size: 24px; color: green;">✔ CARGA EXITOSA</span> Estudiantes matriculados en el periodo actual insertados en la tabla MATRICULADO. <br>';
}
?>