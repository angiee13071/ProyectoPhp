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
    $row = array_map(function($item) {
        return str_replace('"', '', $item);
    }, $row);

    // Agregar la fila a la matriz de datos
    $data_matrix[] = $row;
}

// Insertar los datos en la tabla 'estudiante'
for ($i = 1; $i < count($data_matrix); $i++) {
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
        if ($carrera === "TECNOLOGIA EN SISTEMATIZACION DE DATOS (CICLOS PROPEDEUTICOS)") {
            $id_programa = "578";
        } elseif ($carrera === "INGENIERIA EN TELEMATICA (CICLOS PROPEDEUTICOS)") {
            $id_programa = "678";
        }

        // Asignar el valor de $id_programa a la posición correspondiente en $data_matrix
        $data_matrix[$i][2] = $id_programa;
    }

//DESCOMENTAR API
  //  $ultimo_nombre = array_slice(explode(" ", $nombres), -1)[0];
   // $url = "https://api.genderize.io/?name=" . urlencode($ultimo_nombre);
  //  $response = file_get_contents($url);
   // $data = json_decode($response, true);
   // $genero = isset($data['gender']) ? $data['gender'] : null;

    // Preparar la consulta SQL
    $sql = "INSERT INTO estudiante (id_estudiante, nombres, genero, carrera, documento, estrato, localidad, genero_genero, tipo_inscripcion, estado, id_programa) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Obtener la conexión a la base de datos
    $conn = getDBConnection();

    // Preparar la sentencia
    $stmt = $conn->prepare($sql);

    // Asignar los valores a los parámetros
    $stmt->bind_param("sssssssssss", $id_estudiante, $nombres, $genero, $carrera, $documento, $estrato, $localidad, $genero, $tipo_inscripcion, $estado, $id_programa);

    // Ejecutar la consulta
    $stmt->execute();

    // Cerrar la sentencia y la conexión
    $stmt->close();
    $conn->close();
}

echo "Datos insertados en la tabla 'estudiante'.";
?>