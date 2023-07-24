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
        "TECNOLOGIA EN SISTEMATIZACION DE DATOS (CICLOS PROPEDEUTICOS)" => "578",
        "INGENIERIA EN TELEMATICA (CICLOS PROPEDEUTICOS)" => "678"
    );
    // Verificar si el nombre del programa está en el array de programas válidos
    if (array_key_exists($nombre_programa, $programas_validos)) {
        return $programas_validos[$nombre_programa];
    }

    // Si el nombre del programa no está en los programas válidos, devolver null
    return null;
}

// Verificar si los datos de "CRA. COD" y "CARRERA" ya existen en la tabla "programa"
$sql_check_existing = "SELECT COUNT(*) FROM programa WHERE id_programa = ? AND nombre = ?";
$stmt_check_existing = $conn->prepare($sql_check_existing);
$stmt_check_existing->bind_param("is", $id_programa, $carrera);

// Variable para controlar si hubo algún error durante el proceso de inserción
$insertion_error = false;

// Insertar los datos en la tabla "programa"
$sql_insert_programa = "INSERT INTO programa (id_programa, nombre) VALUES (?, ?)";
$stmt_insert_programa = $conn->prepare($sql_insert_programa);
// Insertar los datos de "CRA. COD" y "CARRERA" en la tabla "programa"
foreach ($carrera_values as $carrera) {
    $id_programa = obtenerIdPrograma($carrera);

    // Verificar si $id_programa es null y asignar el valor adecuado según $carrera
    if ($id_programa === null) {
        // El programa no es válido, continuar con la siguiente iteración
        continue;
    }

    // Verificar si los datos ya existen en la tabla "programa"
    $stmt_check_existing = $conn->prepare("SELECT COUNT(*) FROM programa WHERE id_programa = ? AND nombre = ?");
    $stmt_check_existing->bind_param("is", $id_programa, $carrera);
    $stmt_check_existing->execute();
    $stmt_check_existing->bind_result($existing_count);
    $stmt_check_existing->fetch();

    // Cerrar la sentencia de verificación
    $stmt_check_existing->close();

    // Si los datos no existen en la tabla "programa", insertarlos
    if ($existing_count > 0) {
        // No es necesario hacer nada si ya existen en la tabla
    } else {
        $stmt_insert_programa->bind_param("is", $id_programa, $carrera);
        if (!$stmt_insert_programa->execute()) {
            $insertion_error = true;
            echo "Error al insertar datos en la tabla PROGRAMA: " . $stmt_insert_programa->error, "<br>";
        } else {
            $insertion_error = false;
        }
    }
}

// Cerrar la sentencia de inserción y la conexión a la base de datos
$stmt_insert_programa->close();
$conn->close();

if (!$insertion_error) {
    echo '<span style="font-size: 24px; color: green;">✔ CARGA EXITOSA</span> Datos de programas insertados correctamente en la tabla PROGRAMA. <br>' ;
}

?>