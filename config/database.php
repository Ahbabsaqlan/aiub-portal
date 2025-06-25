<?php
// config/database.php
$host = 'localhost';
$dbname = 'aiub_portal';
$username = 'root'; // Your DB username (default for XAMPP)
$password = ''; // Your DB password (default for XAMPP)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("ERROR: Could not connect to the database. " . $e->getMessage());
}
?>