<?php
// Temporary diagnostic. Upload directly into htdocs and remove after checking.
// Never outputs the password, its hash, phpinfo, or a stack trace.
ini_set('display_errors', '0');
header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: no-store');

if (!is_file(__DIR__ . '/.env')) {
    exit("No se encuentra .env en esta carpeta.\n");
}
if (!is_file(__DIR__ . '/vendor/autoload.php')) {
    exit("No se encuentra vendor/autoload.php en esta carpeta.\n");
}
require __DIR__ . '/vendor/autoload.php';
try {
    $vars = (new CodeIgniter\Config\DotEnv(__DIR__))->parse();
} catch (Throwable $exception) {
    exit("No se pudo interpretar .env. Revisar comillas y formato.\n");
}
$prefix = 'database.default.';
$host = $vars[$prefix . 'hostname'] ?? '';
$user = $vars[$prefix . 'username'] ?? '';
$database = $vars[$prefix . 'database'] ?? '';
$password = $vars[$prefix . 'password'] ?? '';
echo "Archivo .env: encontrado y leido\n";
echo 'Servidor: ' . $host . "\nUsuario: " . $user . "\nBase: " . $database . "\n";
if ($password === '') exit("Contrasena: vacia\n");
if (trim($password, '*') === '') exit("Contrasena: contiene solo asteriscos; se copio el valor oculto.\n");
if (str_contains($password, 'REEMPLAZAR') || str_contains($password, 'TU_CONTRASE')) {
    exit("Contrasena: sigue siendo el marcador de la plantilla.\n");
}
if ($password !== trim($password)) exit("Contrasena: contiene espacios al principio o al final.\n");
if ($host !== 'sql109.infinityfree.com' || $user !== 'if0_43157870' || $database !== 'if0_43157870_los_alces') {
    exit("Los datos del servidor, usuario o base no coinciden con tu cuenta.\n");
}
echo "Contrasena: configurada, sin marcadores ni espacios exteriores\n";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $connection = new mysqli($host, $user, $password, $database, 3306);
    $connection->close();
    echo "Conexion directa: OK\n";
} catch (mysqli_sql_exception $exception) {
    echo 'Conexion directa: ERROR ' . $exception->getCode() . "\n";
    if ($exception->getCode() === 1045) {
        echo "El servidor rechazo las credenciales que contiene este .env.\n";
    }
}
