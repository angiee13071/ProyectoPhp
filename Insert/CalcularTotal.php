<?php
// Incluir el archivo de conexión a la base de datos
// require_once 'ConexionBD.php';
require_once '../ConexionBD.php';
// Crear una instancia de la clase DatabaseConnection
$dbConnection = new DatabaseConnection();
$conn = $dbConnection->getDBConnection();
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

echo '<div style="background-color: #efffef; color: black; padding: 10px; text-align: center;border-radius: 0.8rem;
   border: 2px solid #4CAF50; width: 70rem; position: relative;margin-bottom: 2rem;">
   <span style="font-size: 2rem;color:#4CAF50">✔ CARGA EXITOSA</span><br>
   Retirados insertados correctamente en la tabla RETIRADO.
   <div style="position: absolute; top: 1rem; left: 1rem; font-size: 3rem;color:#4CAF50">⑪</div>
   <div style="position: absolute;  left: 50%;">
    <span style="font-size: 4rem;">&#8595;</span>
   </div>
   </div>';

echo '<div style="background-color: #efffef; color: black; padding: 10px; text-align: center;border-radius: 50rem;
        border: 2px solid #4CAF50; width: 70rem; position: relative;margin-bottom: 2rem;">
        <span style="font-size: 2rem;color:#4CAF50">✔ FIN</span><br>
        Proceso de importación completado
        <div style="position: absolute; top: 1rem; left: 1rem; font-size: 3rem;color:#4CAF50">⑫</div>';
// Cerrar la sentencia y la conexión
$stmt->close();
$conn->close();
