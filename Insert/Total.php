<?php
// Incluir el archivo de conexión a la base de datos
require_once 'conexion.php';
  // Obtener la conexión a la base de datos
  $conn = getDBConnection();
  //Crear procedimiento almacenado:
  
//Llamar al procedimiento almacenado
    $sql = "CALL fill_total()";

    // // Ejecutar la consulta
    // if ($conn->query($sql) === TRUE) {
    //     echo "Tabla total y retirado llenada correctamente.";
    // } else {
    //     echo "Error al ejecutar el procedimiento almacenado: " . $conn->error;
    // }

    
    // Preparar la sentencia
    $stmt = $conn->prepare($sql);

    // Ejecutar la consulta
    $stmt->execute();
    echo "Datos insertados en la tabla 'retirado'.";
 // Cerrar la sentencia y la conexión
 $stmt->close();
 $conn->close();

    ?>