<?php
$host = $GLOBALS['config']['db_host'] ?? 'localhost';
$db   = $GLOBALS['config']['db_name'] ?? 'brique_doree';
$user = $GLOBALS['config']['db_user'] ?? 'root';
$pass = $GLOBALS['config']['db_pass'] ?? 'root';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

global $pdo;

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $error) {
    die("Erreur de connexion à la base de données : " . $error->getMessage());
}