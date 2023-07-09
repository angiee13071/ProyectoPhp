<?php
// Incluir el archivo de conexión a la base de datos
require_once 'conexion.php';

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

// Insertar los datos en la tabla 'graduado'
for ($i = 1; $i < count($data_matrix); $i++) {
    $id_graduado = $i;
    $id_estudiante = $data_matrix[$i][3];
    $id_periodo = 1; // Asigna el ID del periodo correspondiente
    $fecha_grado = $data_matrix[$i][6];
    $promedio = $data_matrix[$i][12];

    // Preparar la consulta SQL
    $sql = "INSERT INTO graduado (id_graduado, id_estudiante, id_periodo, fecha_grado, promedio)
            VALUES (?, ?, ?, ?, ?)";

    // Obtener la conexión a la base de datos
    $conn = getDBConnection();

    // Preparar la sentencia
    $stmt = $conn->prepare($sql);

    // Asignar los valores a los parámetros de la consulta
    $stmt->bind_param("iiiss", $id_graduado, $id_estudiante, $id_periodo, $fecha_grado, $promedio);

    // Ejecutar la consulta
    $stmt->execute();
}

// Cerrar la conexión a la base de datos
$conn->close();

echo "Datos de egresados graduados insertados correctamente en la tabla 'graduado'.";
?>
