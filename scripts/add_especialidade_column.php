<?php
// Migration: add 'especialidade' column to 'dentistas' if missing
// Run this script once via browser (http://your-host/projeto_ASBI-main/scripts/add_especialidade_column.php)

$host = '127.0.0.1';
$port = '3316';
$user = 'root';
$pass = '';
$db   = 'clinica_1';

$mysqli = new mysqli("{$host}:{$port}", $user, $pass, $db);
if ($mysqli->connect_error) {
    die("Connection error: " . $mysqli->connect_error);
}

$col_check = $mysqli->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '{$db}' AND TABLE_NAME = 'dentistas' AND COLUMN_NAME = 'especialidade'");
if ($col_check && $col_check->num_rows > 0) {
    echo "Column 'especialidade' already exists in 'dentistas'.\n";
    exit;
}

$sql = "ALTER TABLE dentistas ADD COLUMN especialidade VARCHAR(255) NULL";
if ($mysqli->query($sql) === TRUE) {
    echo "Column 'especialidade' added successfully to 'dentistas'.\n";
} else {
    echo "Error adding column: " . $mysqli->error . "\n";
}

$mysqli->close();
