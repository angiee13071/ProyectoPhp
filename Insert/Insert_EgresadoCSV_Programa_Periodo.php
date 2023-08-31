<?php
// Incluir el archivo de conexión a la base de datos
// require_once 'ConexionBD.php';
require_once '../ConexionBD.php';
// include "conexion.php";

try {
    $db = new DatabaseConnection();
    $conn = $db->getDBConnection();
    echo '<div style="background-color: #efffef; color: black; padding: 10px; text-align: center;border-radius: 50rem;
        border: 2px solid #4CAF50; width: 70rem; position: relative;margin-bottom: 2rem;">
        <span style="font-size: 2rem;color:#4CAF50"> ✔ INICIO</span><br>
       Conexión a base de datos exitosa
        <div style="position: absolute; top: 1rem; left: 1rem; font-size: 3rem;color:#4CAF50">⓿</div>
        <div style="position: absolute;  left: 50%;">
         <span style="font-size: 4rem;">&#8595;</span>
        </div>
    </div>';
    
    // Aquí puedes realizar operaciones con la base de datos
    
    // $db->closeConnection();
} catch (Exception $e) {
    echo '<div style="background-color: #efffef; color: black; padding: 10px; text-align: center;border-radius: 50rem;
    border: 2px solid rgba(255, 99, 132, 1); width: 70rem; position: relative;margin-bottom: 2rem;">
    <span style="font-size: 2rem; color: rgba(255, 99, 132, 1)">X INICIO ERROR</span><br>
    ' . $e->getMessage() . '
    <div style="position: absolute; top: 1rem; left: 1rem; font-size: 3rem;color:rgba(255, 99, 132, 1)">⓿</div>
    <div style="position: absolute;  left: 50%;">
     <span style="font-size: 4rem;">&#8595;</span>
    </div>
    </div>';
}
// URL del archivo CSV
$url = "file:///C:/xampp/htdocs/ProyectoPhp/Insert/Lista_de_Egresados_por_Proyecto.csv";

$url1 = "file:///C:/xampp/htdocs/ProyectoPhp/Insert/Listado_de_admitidos.csv";
$url2 = "file:///C:/xampp/htdocs/ProyectoPhp/Insert/Lista_de_Egresados_por_Proyecto.csv";
$url3 = "file:///C:/xampp/htdocs/ProyectoPhp/Insert/Listado_matriculados_período_actual.csv";
$url4 = "file:///C:/xampp/htdocs/ProyectoPhp/Insert/Listado_matrículados_a_primer_semestre.csv";
// validar si existen los archivos
if (file_exists($url1) && file_exists($url2) && file_exists($url3) && file_exists($url4)) {
    echo' <div style="background-color: #efffef; color: black; padding: 10px; text-align: center;border: 2px solid #4CAF50;width: 63rem;
     height: 10rem; transform: skew(150deg);margin-bottom: 0rem;">
         <div style="font-size: 3rem; color: #4CAF50;text-align: initial;">❶</div>
         <span style="font-size: 2rem;color:#4CAF50">✔ EXTRACCIÓN Y TRANSFORMACIÓN</span><br>
         <div>Extracción y transformación exitosa de los datos.</div>
         <div style="position: absolute;  left: 50%;">
       
        </div>
     </div>';
     echo' <span style="font-size: 4rem;margin-bottom: 0.5rem;">&#8595;</span>';
    } else{
        echo' <div style="background-color: #efffef; color: black; padding: 10px; text-align: center;border: 2px solid rgba(255, 99, 132, 1);width: 63rem;
        height: 10rem; transform: skew(150deg);margin-bottom: 0rem;">
            <div style="font-size: 3rem; color: rgba(255, 99, 132, 1);text-align: initial;">❶</div>
            <span style="font-size: 2rem;color:rgba(255, 99, 132, 1)">X EXTRACCIÓN Y TRANSFORMACIÓN</span><br>
            <div>No se encuentran todos los archivos en su totalidad, por favor validar.</div>
            <div style="position: absolute;  left: 50%;">
          
           </div>
        </div>';
        echo' <span style="font-size: 4rem;margin-bottom: 0.5rem;">&#8595;</span>';
    }
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
// $sql_insert_periodo = "INSERT INTO periodo (anio, semestre, cohorte) VALUES (?, ?, ?)";
// $stmt_insert_periodo = $conn->prepare($sql_insert_periodo);
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

$skipLines = 2;
// Ejecutar la inserción para cada fila de datos
$unique_years = [];

foreach ($data_matrix as $row) {
    if ($skipLines > 0) {
        // Omitir las primeras líneas
        $skipLines--;
        continue;
    }

    $fecha_grado = isset($row[9]) && !empty($row[9]) ? $row[9] : date('Y-m-d H:i:s');
    $year = date('Y', strtotime($fecha_grado));

    if (!in_array($year, $unique_years)) {
        $unique_years[] = $year;
    }
}

sort($unique_years);

$sql_insert_periodo = "INSERT INTO periodo (anio, semestre, cohorte) VALUES (?, ?, ?)";
$stmt_insert_periodo = $conn->prepare($sql_insert_periodo);


foreach ($unique_years as $year) {
    $cohorte = 1;
    $semestre=1;
    $stmt_insert_periodo->bind_param("iii", $year, $semestre, $cohorte);
    if (!$stmt_insert_periodo->execute()) {
        $insertion_error = true;
      
    }

    $cohorte = 3;
    $semestre=2;
    $stmt_insert_periodo->bind_param("iii", $year, $semestre, $cohorte);
    if (!$stmt_insert_periodo->execute()) {
        $insertion_error = true;
      
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