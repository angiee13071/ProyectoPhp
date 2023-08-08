<?php
// Incluir el archivo de conexión a la base de datos
// require_once 'ConexionBD.php';
require_once '../ConexionBD.php';

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

// Obtener la conexión a la base de datos
$conn = getDBConnection();

// Variable para controlar si hubo algún error durante el proceso de inserción
$insertion_error = false;

// Insertar los datos en la tabla 'graduado'
for ($i = 2; $i < count($data_matrix); $i++) {
    $id_graduado = $i-1;
    $id_estudiante = $data_matrix[$i][6];
    $fecha_grado = $data_matrix[$i][9];
    
    // Calcular el semestre según el mes y el id_periodo
    $year = date('Y', strtotime($fecha_grado));
    $month = date('n', strtotime($fecha_grado));
    $semestre = ($month <= 6) ? 1 : 2;
    $promedio = $data_matrix[$i][15];
    $pasantia = $data_matrix[$i][16];
    // Verificar si el estudiante ya existe en la tabla 'graduado'
    $sql_check_existing = "SELECT COUNT(*) FROM graduado WHERE id_estudiante = ?";
    $stmt_check_existing = $conn->prepare($sql_check_existing);
    $stmt_check_existing->bind_param("s", $id_estudiante);
    $stmt_check_existing->execute();
    $stmt_check_existing->bind_result($existing_count);
    $stmt_check_existing->fetch();
    $stmt_check_existing->close();

    if ($existing_count > 0) {
        $insertion_error = true;
        // El estudiante ya existe en la tabla 'graduado', mostrar una alerta o hacer otra acción si lo deseas
        echo "<span style='font-size: 24px; color: orange;'>¡ALERTA!</span> El estudiante con ID $id_estudiante ya existe en la tabla GRADUADO. Se omitirá la inserción.<br>";
    } else {
     // Consultar el id_periodo que coincide con el año y el semestre
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
        $sql = "INSERT INTO graduado (id_graduado, id_estudiante, id_periodo, fecha_grado, promedio, pasantia)
        VALUES (?, ?, ?, ?, ?, ?)";

        // Preparar la sentencia
        $stmt = $conn->prepare($sql);

        // Asignar los valores a los parámetros de la consulta
        // Ejecutar la consulta
        $stmt->bind_param("iissds", $id_graduado, $id_estudiante, $id_periodo, $fecha_grado, $promedio, $pasantia);


        if (!$stmt->execute()) {
            $insertion_error = true;
            // echo "<span style='font-size: 24px; color: red;'>X ERROR</span> El estudiante con ID $id_estudiante ya existe en la tabla GRADUADO. Se omitirá la inserción.<br>";
            echo "<span style='font-size: 24px; color: red;'>X ERROR</span> El estudiante con ID $id_estudiante no se pudo insertar en la tabla GRADUADO: ". $stmt->error ,"<br>";
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
    echo '<span style="font-size: 24px; color: green;">✔ CARGA EXITOSA</span> Datos de egresados graduados insertados correctamente en la tabla GRADUADO. <br>';
}
?>