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
    $row = array_map(function ($item) {
        return str_replace('"', '', $item);
    }, $row);

    // Agregar la fila a la matriz de datos
    $data_matrix[] = $row;
}

// Obtener los datos únicos de "CRA. COD" y "CARRERA"
$cra_cod_values = array_unique(array_column(array_slice($data_matrix, 2), 1));
$carrera_values = array_unique(array_column(array_slice($data_matrix, 2), 2));

// Función para validar y obtener el id_programa según el nombre del programa
function obtenerIdPrograma($nombre_programa)
{
    // Definir los nombres de los programas válidos
    $programas_validos = array(
        "CRA. COD" => "578",
        "CARRERA" => "678"
    );

    // Verificar si el nombre del programa está en el array de programas válidos
    if (array_key_exists($nombre_programa, $programas_validos)) {
        return $programas_validos[$nombre_programa];
    }

    // Si el nombre del programa no está en los programas válidos, devolver null
    return null;
}

// Insertar los datos en la tabla "programa"
$sql = "INSERT INTO programa (id_programa, nombre) VALUES (?, ?)";
$stmt = $conn->prepare($sql);

// Insertar los datos de "CRA. COD" y "CARRERA" en la tabla "programa"
foreach ($carrera_values as $carrera) {
    $id_programa = obtenerIdPrograma($carrera);

    // Verificar si $id_programa es null y asignar el valor adecuado según $carrera
    if ($id_programa === null) {
        if ($carrera === "TECNOLOGIA EN SISTEMATIZACION DE DATOS (CICLOS PROPEDEUTICOS)") {
            $id_programa = "578";
        } elseif ($carrera === "INGENIERIA EN TELEMATICA (CICLOS PROPEDEUTICOS)") {
            $id_programa = "678";
        } else {
            // Si $carrera no coincide con ninguno de los programas válidos, establecer un valor predeterminado de 0
            $id_programa = "0";
        }
    }

    $stmt->bind_param("is", $id_programa, $carrera);
    $stmt->execute();
}



?>