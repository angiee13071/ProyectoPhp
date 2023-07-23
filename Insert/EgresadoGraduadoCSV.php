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
    $id_estudiante = $data_matrix[$i][6];
    $fecha_grado = $data_matrix[$i][8];
    // Calcular el semestre según el mes y el id_periodo
    $year = date('Y', strtotime($fecha_grado));
    $month = date('n', strtotime($fecha_grado));
    $semestre = ($month <= 6) ? 1 : 2;
    $promedio = $data_matrix[$i][15];

    // Preparar la consulta SQL
    $sql = "INSERT INTO graduado (id_graduado, id_estudiante, id_periodo, fecha_grado, promedio)
            VALUES (?, ?, ?, ?, ?)";

    // Obtener la conexión a la base de datos
    $conn = getDBConnection();
    //para obtener mes y semestre de periodo
     // Escapar los valores para evitar inyecciones SQL (esto depende del tipo de base de datos que estés utilizando)
    $year = mysqli_real_escape_string($conn, $year);
    $semestre = mysqli_real_escape_string($conn, $semestre);
    // Consultar el id_periodo que coincide con el año y el semestre
    $sql_periodo = "SELECT id_periodo FROM periodo WHERE año = '$year' AND semestre = '$semestre'";
    $result_periodo = $conn->query($sql);

    // Verificar si se encontró el id_periodo
    if ($result_periodo && $result_periodo->num_rows > 0) {
        $row = $result_periodo->fetch_assoc();
        $id_periodo = $row['id_periodo'];
    } else {
        // Si no se encontró el id_periodo, se coloca por defecto 1
        $id_periodo = 1;
    }

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