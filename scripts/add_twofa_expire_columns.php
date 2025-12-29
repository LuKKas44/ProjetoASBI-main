<?php
// Migration: add 'twofa_pin_expire' column to 'dentistas' and 'usuario' if missing
$host = '127.0.0.1:3316';
$user = 'root';
$pass = '';
$db   = 'clinica_1';

$mysqli = new mysqli("{$host}", $user, $pass, $db);
if ($mysqli->connect_error) {
    die("Connection error: " . $mysqli->connect_error);
}

$tables = ['dentistas','usuario'];
foreach ($tables as $table) {
    $col_check = $mysqli->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '{$db}' AND TABLE_NAME = '{$table}' AND COLUMN_NAME = 'twofa_pin_expire'");
    if ($col_check && $col_check->num_rows > 0) {
        echo "Column 'twofa_pin_expire' already exists in '{$table}'.\n";
        continue;
    }

    $sql = "ALTER TABLE {$table} ADD COLUMN twofa_pin_expire DATETIME DEFAULT NULL";
    if ($mysqli->query($sql) === TRUE) {
        echo "Column 'twofa_pin_expire' added successfully to '{$table}'.\n";
    } else {
        echo "Error adding column to {$table}: " . $mysqli->error . "\n";
    }
}

$mysqli->close();
