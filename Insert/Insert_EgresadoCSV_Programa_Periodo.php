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
// Insertar los datos en la tabla periodo
$sql_insert_periodo = "INSERT INTO periodo (anio, semestre, cohorte) VALUES (?, ?, ?)";
$stmt_insert_periodo = $conn->prepare($sql_insert_periodo);
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
// Ejecutar la inserción para cada fila de datos
foreach ($data_matrix as $row) {
    // Calcular el semestre según el mes y el id_periodo
    $fecha_grado = isset($row[9]) ? $row[9] : null;
    $year = date('Y', strtotime($fecha_grado));
    $month = date('n', strtotime($fecha_grado));
    $semestre = ($month <= 6) ? 1 : 2;
    $cohorte = ($month <= 6) ? 1 : 3;

    // Verificar si el período ya existe en la tabla periodo
    $sql_check_periodo = "SELECT COUNT(*) FROM periodo WHERE anio = ? AND semestre = ?";
    $stmt_check_periodo = $conn->prepare($sql_check_periodo);
    $stmt_check_periodo->bind_param("ii", $year, $semestre);
    $stmt_check_periodo->execute();
    $stmt_check_periodo->bind_result($existing_count);
    $stmt_check_periodo->fetch();
    $stmt_check_periodo->close();

    if ($existing_count > 0) {
        // El período ya existe, omitir la inserción
        continue;
    }

    // Insertar en la tabla periodo
    $stmt_insert_periodo->bind_param("iii", $year, $semestre, $cohorte);
    if (!$stmt_insert_periodo->execute()) {
        $insertion_error = true;
        echo "Error al insertar datos en la tabla PERIODO: " . $stmt_insert_periodo->error, "<br>";
    }
}
// Cerrar la sentencia de inserción y la conexión a la base de datos
$stmt_insert_programa->close();
$stmt_insert_periodo->close();
$conn->close();

if (!$insertion_error) {
    //echo '<span style="font-size: 24px; color: green;">✔ CARGA EXITOSA</span> Datos de programas insertados correctamente en la tabla PROGRAMA. <br>' ;
//     echo '<div style="background-color: #4CAF50; color: white; padding: 10px; text-align: center;">
//     <span style="font-size: 24px;">✔ CARGA EXITOSA</span><br>
//     Datos de programas insertados correctamente en la tabla PROGRAMA.
// </div>';
echo '<div style="background-color: #efffef; color: black; padding: 10px; text-align: center;border-radius: 0.8rem;
border: 2px solid #4CAF50; width: 70rem; position: relative;margin-bottom: 2rem;">
<span style="font-size: 2rem;color:#4CAF50">✔ CARGA EXITOSA</span><br>
Carreras insertadas correctamente en la tabla PROGRAMA.
<div style="position: absolute; top: 1rem; left: 1rem; font-size: 3rem;color:#4CAF50">❷</div>
<div style="position: absolute;  left: 50%;">
 <span style="font-size: 4rem;">&#8595;</span>
</div>
</div>';
}

?>