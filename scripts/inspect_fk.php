<?php

$path = __DIR__ . '/../database/database.sqlite';
if (!file_exists($path)) {
    fwrite(STDERR, "Database file not found: $path\n");
    exit(1);
}

$db = new PDO('sqlite:' . $path);
$rows = $db->query('PRAGMA foreign_key_list(reservations)')->fetchAll(PDO::FETCH_ASSOC);
print_r($rows);
