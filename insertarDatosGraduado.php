<?php
// URL del archivo CSV
$url = 'https://cdn-153.anonfiles.com/I3i7L6yczc/c3f5755e-1687810046/Lista_de_Egresados_por_Proyecto.csv';

// Abre el archivo CSV
$file = fopen($url, 'r');

if ($file === false) {
    die('Error al abrir el archivo CSV');
}

// Leer el archivo CSV línea por línea
while (($data = fgetcsv($file)) !== false) {
    // Imprime los datos
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

// Cierra el archivo CSV
fclose($file);

echo 'Lectura del CSV completada.';
?>
