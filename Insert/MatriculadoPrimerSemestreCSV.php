<?php
// Incluir el archivo de conexión a la base de datos
require_once 'conexion.php';

// URL del archivo CSV
$url = "file:///C:/xampp/htdocs/ProyectoPhp/Insert/Listado_matrículados_a_primer_semestre.csv";

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

// Mostrar los datos del CSV por pantalla
echo "Datos del CSV:<br>";
for ($i = 1; $i < count($data_matrix); $i++) {
    echo "No.: " . $data_matrix[$i][3] . "<br>";
    echo "Código: " . $data_matrix[$i][4] . "<br>";
    echo "Nombre: " . $data_matrix[$i][5] . "<br>";
    echo "No. Identificación: " . $data_matrix[$i][6] . "<br>";
    echo "Pensum: " . $data_matrix[$i][7] . "<br>";
    echo "Tipo Estudiante: " . $data_matrix[$i][8] . "<br>";
    echo "Acuerdo: " . $data_matrix[$i][9] . "<br>";
    echo "Correo Electrónico: " . $data_matrix[$i][10] . "<br>";
    echo "Estado Actual: " . $data_matrix[$i][11] . "<br>";
    echo "Detalle Estado: " . $data_matrix[$i][12] . "<br>";
    echo "<br>";
}

// Insertar los datos en la tabla 'primiparo'
 // Variable para asignar un valor único a cada registro
for ($i = 1; $i < count($data_matrix); $i++) {
    $id_primiparo = $data_matrix[$i][3];
    $id_estudiante = $data_matrix[$i][4];
    $id_periodo = 2; // Asigna el ID del periodo correspondiente

    // Preparar la consulta SQL
    $sql = "INSERT INTO primiparo (id_primiparo, id_estudiante, id_periodo)
            VALUES (?, ?, ?)";

    // Obtener la conexión a la base de datos
    $conn = getDBConnection();

    // Preparar la sentencia
    $stmt = $conn->prepare($sql);

    // Asignar los valores a los parámetros
    $stmt->bind_param("iii", $id_primiparo, $id_estudiante, $id_periodo);

    // Ejecutar la consulta
    $stmt->execute();
    // Cerrar la sentencia y la conexión
    $stmt->close();
    $conn->close();
}

echo "Datos insertados en la tabla 'primiparo'.";

?>