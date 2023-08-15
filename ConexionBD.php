<?php
// Datos de conexión a la base de datos
$host = "127.0.0.1";
$port = 3306;
$socket = "";
$user = "root";
$password = "";
$dbname = "Permanencia_Desercion";

// Función para obtener la conexión a la base de datos
error_reporting(0);
function getDBConnection() {
    global $host, $port, $socket, $user, $password, $dbname;

    $con = new mysqli($host, $user, $password, $dbname, $port, $socket)
        or die('Could not connect to the database server' . mysqli_connect_error());
       
        // error_reporting(E_ALL);
        if ($con->connect_error) {
            echo '<div style="background-color: #efffef; color: black; padding: 10px; text-align: center;border-radius: 50rem;
            border: 2px solid rgba(255, 99, 132, 1); width: 70rem; position: relative;margin-bottom: 2rem;">
            <span style="font-size: 2rem; color: rgba(255, 99, 132, 1)">X INICIO ERROR</span><br>
            No se puede establecer una conexión ya que el equipo de destino denegó expresamente dicha conexión
            <div style="position: absolute; top: 1rem; left: 1rem; font-size: 3rem;color:rgba(255, 99, 132, 1)">⓿</div>
            <div style="position: absolute;  left: 50%;">
             <span style="font-size: 4rem;">&#8595;</span>
            </div>
            </div>';
            echo '<div style="background-color: #efffef; color: black; padding: 10px; text-align: center;border-radius: 50rem;
border: 2px solid rgba(255, 99, 132, 1); width: 70rem; position: relative;margin-bottom: 2rem;">
<span style="font-size: 2rem; color: rgba(255, 99, 132, 1)">X FIN ERROR</span><br>
Proceso de importación completado
<div style="position: absolute; top: 1rem; left: 1rem; font-size: 3rem;color:rgba(255, 99, 132, 1)">⓫</div>
<div style="position: absolute;  left: 50%;">
</div>
</div>';

            die();
        }
        echo '<div style="background-color: #efffef; color: black; padding: 10px; text-align: center;border-radius: 50rem;
        border: 2px solid #4CAF50; width: 70rem; position: relative;margin-bottom: 2rem;">
        <span style="font-size: 2rem;color:#4CAF50"> ✔ INICIO</span><br>
       Conexión a base de datos exitosa
        <div style="position: absolute; top: 1rem; left: 1rem; font-size: 3rem;color:#4CAF50">⓿</div>
        <div style="position: absolute;  left: 50%;">
         <span style="font-size: 4rem;">&#8595;</span>
        </div>
        </div>';
    
      
    
        return $con;
    }



$conn = getDBConnection();
// $conn->close(); // Elimina esta línea para evitar cerrar la conexión aquí
?>