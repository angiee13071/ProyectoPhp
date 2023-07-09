<?php
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
    $row = array_map(function($item) {
        return str_replace('"', '', $item);
    }, $row);

    // Agregar la fila a la matriz de datos
    $data_matrix[] = $row;
}

// Mostrar los datos del CSV por pantalla
echo "Datos del CSV:<br>";
for ($i = 1; $i < count($data_matrix); $i++) {
    echo "No.: " . $data_matrix[$i][4] . "<br>";
    echo "Código: " . $data_matrix[$i][5] . "<br>";
    echo "Nombre: " . $data_matrix[$i][6] . "<br>";
    echo "No. Identificación: " . $data_matrix[$i][7] . "<br>";
    echo "Pensum: " . $data_matrix[$i][8] . "<br>";
    echo "Acuerdo: " . $data_matrix[$i][9] . "<br>";
    echo "Tipo Estudiante: " . $data_matrix[$i][10] . "<br>";
    echo "Código Estado: " . $data_matrix[$i][11] . "<br>";
    echo "Descripción Estado: " . $data_matrix[$i][12] . "<br>";
    echo "Correo: " . $data_matrix[$i][13] . "<br>";
    echo "Correo Institucional: " . $data_matrix[$i][14] . "<br>";
    echo "Estrato: " . $data_matrix[$i][15] . "<br>";
    echo "Localidad: " . $data_matrix[$i][16] . "<br>";
    echo "<br>";
}

// Insertar los datos en la tabla 'matriculado'
$id_matricula = 1; // Variable para asignar un valor único a cada registro
for ($i = 1; $i < count($data_matrix); $i++) {
    $id_estudiante = $data_matrix[$i][7];
    $id_periodo = 1;
    $estado_matricula = $data_matrix[$i][12];

    // Preparar la consulta SQL
    $sql = "INSERT INTO matriculado (id_matricula, id_estudiante, id_periodo, estado_matricula)
            VALUES (?, ?, ?, ?)";

    // Obtener la conexión a la base de datos
    $conn = getDBConnection();

    // Preparar la sentencia
    $stmt = $conn->prepare($sql);

    // Asignar los valores a los parámetros
    $stmt->bind_param("iiis", $id_matricula, $id_estudiante, $id_periodo, $estado_matricula);

    // Ejecutar la consulta
    $stmt->execute();

    // Incrementar el valor de $id_matricula para el siguiente registro
    $id_matricula++;

    // Cerrar la sentencia y la conexión
    $stmt->close();
    $conn->close();
}

echo "Datos insertados en la tabla 'matriculado'.";


?>

