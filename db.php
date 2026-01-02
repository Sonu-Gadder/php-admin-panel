<?php
// Database Configuration
$host = 'localhost';
$db   = 'hiring_platform';
$user = 'root'; 
$pass = ''; // Default for XAMPP/Laragon is empty. If you set a password, enter it here.

try {
    // Establishing PDO connection with UTF-8 character set
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    
    // Set error mode to Exception so we can catch database errors
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set default fetch mode to Associative Array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // If connection fails, stop the script and show error
    die("CRITICAL SYSTEM ERROR: Database connection could not be established. " . $e->getMessage());
}
?>