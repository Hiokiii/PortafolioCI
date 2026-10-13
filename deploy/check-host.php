<?php
// Temporary standalone check: upload to htdocs, inspect, then remove.
// Does not read credentials, query the database, or expose phpinfo().
header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: no-store');
echo 'PHP: ' . PHP_VERSION . PHP_EOL;
foreach (['intl', 'mbstring', 'mysqli', 'openssl', 'dom', 'gd'] as $extension) {
    echo $extension . ': ' . (extension_loaded($extension) ? 'OK' : 'FALTA') . PHP_EOL;
}
echo PHP_EOL . 'Directorios de la aplicacion (despues de subir):' . PHP_EOL;
foreach (['writable/cache', 'writable/logs', 'writable/session', 'writable/uploads'] as $directory) {
    echo $directory . ': ' . (is_dir(__DIR__ . '/' . $directory)
        && is_writable(__DIR__ . '/' . $directory) ? 'OK' : 'FALTA O NO ES ESCRIBIBLE') . PHP_EOL;
}
