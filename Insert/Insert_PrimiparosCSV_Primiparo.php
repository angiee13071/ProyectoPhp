<?php
set_time_limit(300);
// Incluir el archivo de conexión a la base de datos
// require_once 'ConexionBD.php';
require_once '../ConexionBD.php';


// URL del archivo CSV
$url = "file:///C:/xampp/htdocs/ProyectoPhp/Insert/Listado_matrículados_a_primer_semestre.csv";

// Obtener el contenido del archivo en un array
$file_data = file($url, FILE_IGNORE_NEW_LINES);

// Variable para controlar si hubo algún error durante el proceso de inserción
$insertion_error = false;

// Abrir la conexión a la base de datos
$conn = getDBConnection();

// Insertar los datos en la tabla 'primiparo'
for ($i = 2; $i < count($file_data); $i++) {
    // Dividir la línea en sus elementos utilizando la coma como separador
    $row = explode(",", $file_data[$i]);

    // Eliminar las comillas de cada dato
    $row = array_map('trim', $row);
    $row = array_map(function ($item) {
        return str_replace('"', '', $item);
    }, $row);
 
    $id_primiparo = $i-1;
    $id_estudiante = $row[4];
   
    $periodo = $row[2];; // Asigna el ID del periodo correspondiente
  
 // Extraer el año y el semestre del periodo
 $year_semester = explode('-', $periodo);
 if (count($year_semester) >= 2) {
    $anio = $year_semester[0];
    $semestre = $year_semester[1];
} else {
 
    continue; // Saltar a la siguiente iteración del bucle
}
//  $anio = $year_semester[0];
//  $semestre = $year_semester[1];

 // Buscar el id_periodo en la tabla 'periodo' utilizando el año y el semestre
 $sql_get_id_periodo = "SELECT id_periodo FROM periodo WHERE anio = ? AND semestre = ?";
 $stmt_get_id_periodo = $conn->prepare($sql_get_id_periodo);
 $stmt_get_id_periodo->bind_param("ii", $anio, $semestre);
 $stmt_get_id_periodo->execute();
 $stmt_get_id_periodo->bind_result($id_periodo);
 $stmt_get_id_periodo->fetch();
 $stmt_get_id_periodo->close();
    // Verificar si el estudiante ya existe en la tabla 'primiparo'
    $sql_check_existing = "SELECT COUNT(*) FROM primiparo WHERE id_primiparo = ?";
    $stmt_check_existing = $conn->prepare($sql_check_existing);
    $stmt_check_existing->bind_param("i", $id_primiparo);
    $stmt_check_existing->execute();
    $stmt_check_existing->bind_result($existing_count);
    $stmt_check_existing->fetch();
    $stmt_check_existing->close();
    if (!empty($id_primiparo) && !empty($id_estudiante) && !empty($periodo)) {
        if ($existing_count > 0) {
            // El estudiante ya existe en la tabla 'primiparo', mostrar una alerta o hacer otra acción si lo deseas
            //echo "<span style='font-size: 24px; color: orange;'>¡ALERTA!</span> El estudiante matriculado actualmente con ID $id_estudiante ya existe en la tabla PRIMIPARO. Se omitirá la inserción.<br>"; 
            $insertion_error = true;
            echo '<div style="background-color: #FBFFBA; color: black; padding: 10px; text-align: center;border-radius: 0.8rem;
        border: 2px solid orange; width: 70rem; position: relative;margin-bottom: 2rem;">
        <span style="font-size: 2rem;color:orange">¡ALERTA!</span><br>
        El estudiante matriculado actualmente con ID $id_estudiante ya existe en la tabla PRIMIPARO. Se omitirá la inserción.
        <div style="position: absolute; top: 1rem; left: 1rem; font-size: 3rem;color:orange">❻</div>
        <div style="position: absolute;  left: 50%;">
         <span style="font-size: 4rem;">&#8595;</span>
        </div>
        </div>'; 
        } else {
            // Preparar la consulta SQL para insertar el estudiante en la tabla 'primiparo'
            $sql_insert = "INSERT INTO primiparo (id_primiparo, id_estudiante, id_periodo) VALUES (?, ?, ?)";
    
            // Preparar la sentencia
            $stmt_insert = $conn->prepare($sql_insert);
    
            // Asignar los valores a los parámetros
            $stmt_insert->bind_param("iii", $id_primiparo, $id_estudiante, $id_periodo);
    
            // Ejecutar la consulta
            if (!$stmt_insert->execute()) {
                // Hubo un error durante la inserción
                //TODO:Validar los no insertados
               $insertion_error =true;
                //echo "<span style='font-size: 24px; color: red;'>X ERROR</span> El estudiante nuevo con ID $id_primiparo, $id_estudiante, $id_periodo no se pudo insertar en la tabla PRIMIPARO: ". $stmt_insert->error ,"<br>";
                echo '<div style="background-color: #FFE1E1; color: black; padding: 10px; text-align: center;border-radius: 0.8rem;
                border: 2px solid rgba(255, 99, 132, 1); width: 70rem; position: relative;margin-bottom: 2rem;">
                <span style="font-size: 2rem;color:rgba(255, 99, 132, 1)">X ERROR</span><br>
                El estudiante nuevo con ID $id_primiparo, $id_estudiante, $id_periodo no se pudo insertar en la tabla PRIMIPARO: ". $stmt_insert->error ,"<br>";
                <div style="position: absolute; top: 1rem; left: 1rem; font-size: 3rem;color:rgba(255, 99, 132, 1)">❻</div>
                <div style="position: absolute;  left: 50%;">
                 <span style="font-size: 4rem;">&#8595;</span>
                </div>
                </div>'; 
            }else{
                $insertion_error =false;
            }
    
            // Cerrar la sentencia
            $stmt_insert->close();
        }
    }else {
        echo "<span style='font-size: 24px; color: orange;'>¡ALERTA!</span> Datos incompletos para la fila $i. Se omitirá la inserción en la tabla PRIMIPARO.<br>";
        $insertion_error = true;
    }
   
}

// Cerrar la conexión a la base de datos después de haber procesado todos los datos
$conn->close();

if (!$insertion_error) {

    //echo '<span style="font-size: 24px; color: green;">✔ CARGA EXITOSA</span> Datos de estudiantes nuevo insertados en la tabla PRIMIPARO.  <br>';
    echo '<div style="background-color: #efffef; color: black; padding: 10px; text-align: center;border-radius: 0.8rem;
    border: 2px solid #4CAF50; width: 70rem; position: relative;margin-bottom: 2rem;">
    <span style="font-size: 2rem;color:#4CAF50">✔ CARGA EXITOSA</span><br>
    Primiparos insertados correctamente en la tabla PRIMIPARO.
    <div style="position: absolute; top: 1rem; left: 1rem; font-size: 3rem;color:#4CAF50">❻</div>
    <div style="position: absolute;  left: 50%;">
     <span style="font-size: 4rem;">&#8595;</span>
    </div>
    </div>';
}

?>