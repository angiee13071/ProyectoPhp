<?php
set_time_limit(300);

// Incluir el archivo de conexión a la base de datos
require_once 'conexion.php';

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
$insertion_error = false;

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
        // El estudiante ya existe en la base de datos, mostrar una alerta o hacer otra acción si lo deseas
        // echo "<span style='font-size: 24px; color: orange;'>¡ALERTA!</span> El estudiante matriculado actualmente con ID $id_estudiante ya existe en la tabla ESTUDIANTE. Se omitirá la inserción.<br>";
        $insertion_error = true;
    } else {
        // Preparar la consulta SQL para insertar el estudiante
        $sql_insert = "INSERT INTO estudiante (id_estudiante, nombres, genero, carrera, documento, estrato, localidad, genero_genero, tipo_inscripcion, estado, id_programa) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // Preparar la sentencia
        $stmt_insert = $conn->prepare($sql_insert);

        // Asignar los valores a los parámetros
        $stmt_insert->bind_param("sssssssssss", $id_estudiante, $nombres, $genero, $carrera, $documento, $estrato, $localidad, $genero, $tipo_inscripcion, $estado, $id_programa);

        // Ejecutar la consulta
        if (!$stmt_insert->execute()) {
            // Hubo un error durante la inserción
            $insertion_error = true;
            echo "<span style='font-size: 24px; color: red;'>X ERROR</span> El estudiante con ID $id_estudiante no se pudo insertar en la tabla ESTUDIANTE: " . $stmt_insert->error, "<br>";
        } else {
            $insertion_error = false;
        }
        // Cerrar la sentencia (no es necesario cerrar la conexión en este punto)
        $stmt_insert->close();
    }
}

// Cerrar la conexión a la base de datos después de haber procesado todos los datos
$conn->close();

if (!$insertion_error) {
    echo '<span style="font-size: 24px; color: green;">✔ CARGA EXITOSA</span> Estudiantes matriculados en el periodo actual insertados en la tabla ESTUDIANTE. <br>';
}
?>