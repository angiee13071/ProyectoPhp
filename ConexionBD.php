<?php
// Datos de conexión a la base de datos
$host = "127.0.0.1";
$port = 3306;
$socket = "";
$user = "root";
$password = "";
$dbname = "Permanencia_Desercion";

// Función para obtener la conexión a la base de datos
function getDBConnection() {
    global $host, $port, $socket, $user, $password, $dbname;

    $con = new mysqli($host, $user, $password, $dbname, $port, $socket)
        or die('Could not connect to the database server' . mysqli_connect_error());

    return $con;
}

$conn = getDBConnection();
// $conn->close(); // Elimina esta línea para evitar cerrar la conexión aquí
?>