<?php
// Generate a repeatable repair for an interrupted demo import; no data is deleted.
$source = file_get_contents(__DIR__ . '/../club_de_futbol.sql');
preg_match_all('/ALTER TABLE `([^`]+)`\s+(.*?);/s', $source, $statements, PREG_SET_ORDER);
if (count($statements) !== 70) throw new RuntimeException('Unexpected number of schema statements');

function repairLiteral(string $value): string
{
    return "'" . str_replace(["\\", "'"], ["\\\\", "''"], $value) . "'";
}

$output = "-- Repeatable repair for the imported Los Alces demo database.\n"
    . "-- Only missing schema items are added. Existing data is preserved.\n"
    . "SET NAMES utf8mb4;\n";
$operations = 0;
foreach ($statements as $statement) {
    $table = $statement[1];
    $body = trim($statement[2]);
    $clauses = str_starts_with($body, 'MODIFY') ? [$body] : preg_split('/,\s*(?=ADD\s)/i', $body);
    foreach ($clauses as $clause) {
        $clause = trim($clause);
        $schemaMatch = 'TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ' . repairLiteral($table);
        if (preg_match('/^ADD PRIMARY KEY/', $clause)) {
            $exists = 'SELECT 1 FROM information_schema.STATISTICS WHERE ' . $schemaMatch . " AND INDEX_NAME = 'PRIMARY'";
        } elseif (preg_match('/^ADD (?:UNIQUE )?KEY `([^`]+)`/', $clause, $match)) {
            $exists = 'SELECT 1 FROM information_schema.STATISTICS WHERE ' . $schemaMatch . ' AND INDEX_NAME = ' . repairLiteral($match[1]);
        } elseif (preg_match('/^ADD CONSTRAINT `([^`]+)`/', $clause, $match)) {
            $exists = 'SELECT 1 FROM information_schema.TABLE_CONSTRAINTS WHERE ' . $schemaMatch . ' AND CONSTRAINT_NAME = ' . repairLiteral($match[1]);
        } elseif (preg_match('/^MODIFY `([^`]+)`.*AUTO_INCREMENT/s', $clause, $match)) {
            $exists = 'SELECT 1 FROM information_schema.COLUMNS WHERE ' . $schemaMatch
                . ' AND COLUMN_NAME = ' . repairLiteral($match[1]) . " AND EXTRA LIKE '%auto_increment%'";
        } else {
            throw new RuntimeException('Unrecognized schema clause: ' . $clause);
        }
        $alter = 'ALTER TABLE `' . $table . '` ' . $clause;
        $output .= "\n-- $table\nSET @demo_repair_sql = IF(EXISTS($exists), 'SELECT 1 AS ya_configurado', "
            . repairLiteral($alter) . ");\n"
            . "PREPARE demo_repair FROM @demo_repair_sql;\nEXECUTE demo_repair;\nDEALLOCATE PREPARE demo_repair;\n";
        $operations++;
    }
}
$output .= "\nSELECT 'Reparacion completada' AS estado;\n";
$target = __DIR__ . '/../dist/reparar_importacion_demo.sql';
if (!is_dir(dirname($target))) mkdir(dirname($target), 0775, true);
file_put_contents($target, $output);
echo "Generated $operations conditional schema operations.\n";
