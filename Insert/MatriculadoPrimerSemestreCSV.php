<?php
set_time_limit(300);
// Incluir el archivo de conexión a la base de datos
require_once 'conexion.php';

// URL del archivo CSV
$url = "file:///C:/xampp/htdocs/ProyectoPhp/Insert/Listado_matrículados_a_primer_semestre.csv";

// Obtener el contenido del archivo en un array
$file_data = file($url, FILE_IGNORE_NEW_LINES);

// Variable para controlar si hubo algún error durante el proceso de inserción
$insertion_error = false;

// Abrir la conexión a la base de datos
$conn = getDBConnection();

// Insertar los datos en la tabla 'primiparo'
for ($i = 2; $i < count($file_data); $i++) {
    // Dividir la línea en sus elementos utilizando la coma como separador
    $row = explode(",", $file_data[$i]);

    // Eliminar las comillas de cada dato
    $row = array_map('trim', $row);
    $row = array_map(function ($item) {
        return str_replace('"', '', $item);
    }, $row);

    $id_primiparo = $row[3];
    $id_estudiante = $row[4];
    $id_periodo = 2; // Asigna el ID del periodo correspondiente

    // Verificar si el estudiante ya existe en la tabla 'primiparo'
    $sql_check_existing = "SELECT COUNT(*) FROM primiparo WHERE id_primiparo = ?";
    $stmt_check_existing = $conn->prepare($sql_check_existing);
    $stmt_check_existing->bind_param("i", $id_primiparo);
    $stmt_check_existing->execute();
    $stmt_check_existing->bind_result($existing_count);
    $stmt_check_existing->fetch();
    $stmt_check_existing->close();

    if ($existing_count > 0) {
        // El estudiante ya existe en la tabla 'primiparo', mostrar una alerta o hacer otra acción si lo deseas
        echo "<span style='font-size: 24px; color: orange;'>¡ALERTA!</span> El estudiante matriculado actualmente con ID $id_estudiante ya existe en la tabla PRIMIPARO. Se omitirá la inserción.<br>"; 
        $insertion_error = true;
    } else {
        // Preparar la consulta SQL para insertar el estudiante en la tabla 'primiparo'
        $sql_insert = "INSERT INTO primiparo (id_primiparo, id_estudiante, id_periodo) VALUES (?, ?, ?)";

        // Preparar la sentencia
        $stmt_insert = $conn->prepare($sql_insert);

        // Asignar los valores a los parámetros
        $stmt_insert->bind_param("iii", $id_primiparo, $id_estudiante, $id_periodo);

        // Ejecutar la consulta
        if (!$stmt_insert->execute()) {
            // Hubo un error durante la inserción
            //TODO:Validar los no insertados
           // $insertion_error =true;
            //echo "<span style='font-size: 24px; color: red;'>X ERROR</span> El estudiante nuevo con ID $id_estudiante no se pudo insertar en la tabla PRIMIPARO: ". $stmt_insert->error ,"<br>";
        }else{
            $insertion_error =false;
        }

        // Cerrar la sentencia
        $stmt_insert->close();
    }
}

// Cerrar la conexión a la base de datos después de haber procesado todos los datos
$conn->close();

if (!$insertion_error) {

    echo '<span style="font-size: 24px; color: green;">✔ CARGA EXITOSA</span> Datos de estudiantes nuevo insertados en la tabla PRIMIPARO.  <br>';
}

?>