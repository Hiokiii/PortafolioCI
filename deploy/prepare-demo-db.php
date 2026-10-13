<?php
// Run locally with PHP. The output is for importing into an EMPTY demo database.
require __DIR__ . '/../vendor/codeigniter4/framework/system/Test/bootstrap.php';

function sqlString(string $value): string
{
    return "'" . str_replace(["\\", "'"], ["\\\\", "''"], $value) . "'";
}

function splitSqlRow(string $row): array
{
    $tokens = [];
    $token = '';
    $quoted = false;
    for ($i = 0, $length = strlen($row); $i < $length; $i++) {
        $char = $row[$i];
        if ($quoted && $char === "\\") {
            $token .= $char . $row[++$i];
            continue;
        }
        if ($char === "'") {
            if ($quoted && ($row[$i + 1] ?? '') === "'") {
                $token .= "''";
                $i++;
                continue;
            }
            $quoted = !$quoted;
        }
        if ($char === ',' && !$quoted) {
            $tokens[] = trim($token);
            $token = '';
        } else {
            $token .= $char;
        }
    }
    if ($quoted) throw new RuntimeException('SQL string is not closed');
    $tokens[] = trim($token);
    return $tokens;
}

$source = file_get_contents(__DIR__ . '/../club_de_futbol.sql');
$encrypter = Config\Services::encrypter();
$counts = [];
$accounts = [];
$roleEmails = ['administrador' => 'admin', 'socio' => 'socio', 'direccion' => 'direccion',
    'jugador' => 'jugador', 'equipo_tecnico' => 'tecnico'];
$seenRoles = [];
$output = preg_replace_callback('/INSERT INTO `([^`]+)`\s*\(([^;]+?)\)\s*VALUES\s*(.*?);/s',
    function ($insert) use ($encrypter, &$counts, &$accounts, &$seenRoles, $roleEmails) {
        preg_match_all('/`([^`]+)`/', $insert[2], $columnMatches);
        $columns = $columnMatches[1];
        preg_match_all("/\\((?:'(?:\\\\.|''|[^'\\\\])*'|[^')])*\\)/s", $insert[3], $rowMatches);
        if (!$rowMatches[0]) throw new RuntimeException('No SQL rows: ' . $insert[1]);
        $rows = [];
        foreach ($rowMatches[0] as $index => $row) {
            $values = splitSqlRow(substr($row, 1, -1));
            if (count($values) !== count($columns)) throw new RuntimeException('Column mismatch: ' . $insert[1]);
            $id = (int) $values[array_search('id', $columns, true)];
            if ($insert[1] === 'usuarios') {
                $role = trim($values[array_search('rol', $columns, true)], "'");
                $email = isset($seenRoles[$role]) ? "usuario$id@example.com" : $roleEmails[$role] . '@example.com';
                $seenRoles[$role] = true;
                $replacements = ['nombres' => "Usuario $id", 'apellidos' => 'Demo', 'email' => $email,
                    'run' => 'DEMO-' . $id, 'direccion' => "Direccion ficticia $id", 'telefono' => '0',
                    'password_hash' => bin2hex($encrypter->encrypt('DemoAlces2026!'))];
                foreach ($replacements as $column => $value) {
                    $values[array_search($column, $columns, true)] = $column === 'telefono' ? $value : sqlString($value);
                }
                $accounts[] = [$id, $role, $email];
            }
            foreach ($columns as $position => $column) {
                if ($values[$position] === 'NULL') continue;
                if ($column === 'detalle' || $column === 'condiciones') {
                    $values[$position] = sqlString("Dato ficticio de demo $id");
                }
                if (in_array($column, ['jugador_visita', 'jugador_externo', 'nombre_jugador_saliente', 'nombre_jugador_entrante'], true)) {
                    $values[$position] = sqlString("Jugador visitante demo $id");
                }
            }
            $rows[] = '(' . implode(', ', $values) . ')';
        }
        $counts[$insert[1]] = count($rows);
        return 'INSERT INTO `' . $insert[1] . '` (' . $insert[2] . ") VALUES\n" . implode(",\n", $rows) . ';';
    }, $source);

// Remove the local root definer and repair an invalid ORDER BY in the old dump.
$output = preg_replace('/DEFINER=`[^`]+`@`[^`]+`\s+SQL SECURITY DEFINER/', 'SQL SECURITY INVOKER', $output);
$output = str_replace('ORDER BY `p`.`fecha` AS `DESCdesc` ASC', 'ORDER BY `p`.`fecha` DESC', $output);
if (count($seenRoles) !== 5) throw new RuntimeException('Missing demo roles');
if (str_contains($output, 'DEFINER=') || str_contains($output, 'DESCdesc')) throw new RuntimeException('Invalid view');

// No CREATE VIEW privilege on InfinityFree. Preserve the tail for resuming
// the first failed import, and exclude the view from future fresh imports.
if (!preg_match('/CREATE ALGORITHM=.*?VIEW `vista_partidos_hasta_hoy`.*?;/s', $output, $view, PREG_OFFSET_CAPTURE)) {
    throw new RuntimeException('Expected view statement is missing');
}
$viewEnd = $view[0][1] + strlen($view[0][0]);
$resume = substr($output, $viewEnd);
$sessionSetup = substr($output, 0, strpos($output, 'CREATE TABLE'));
$output = preg_replace('/CREATE TABLE `vista_partidos_hasta_hoy`\s*\(.*?\);/s', '', $output);
$output = preg_replace('/DROP TABLE IF EXISTS `vista_partidos_hasta_hoy`\s*;/', '', $output);
$output = preg_replace('/CREATE ALGORITHM=.*?VIEW `vista_partidos_hasta_hoy`.*?;/s', '', $output);

$directory = __DIR__ . '/../dist';
if (!is_dir($directory)) mkdir($directory, 0775, true);
file_put_contents($directory . '/los_alces_demo.sql', "-- Demo database: fictional users and contact details.\n" . $output);
file_put_contents($directory . '/completar_importacion_demo.sql',
    "-- Run ONCE after the original import stopped at CREATE VIEW.\n" . $sessionSetup . $resume);
$readme = "# Usuarios ficticios de la demo\n\nClave de todos: `DemoAlces2026!`\n\n| Rol | Correo |\n|---|---|\n";
foreach ($roleEmails as $role => $email) $readme .= "| $role | $email@example.com |\n";
$readme .= "\nImportar los_alces_demo.sql en una base vacia. No subir el SQL a htdocs.\n";
file_put_contents($directory . '/USUARIOS-DEMO.md', $readme);
echo 'Generated demo SQL: ' . $counts['usuarios'] . ' fictional users; ' . array_sum($counts) . ' total rows.' . PHP_EOL;
