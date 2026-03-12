<?php
/**
 * Ansluter till MySQL-databasen
 */

// Databasinstallningar
$host = 'localhost';
$dbname = 'kvitter';
$username = 'root';
$password = 'root';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

// PDO-alternativ for säkrare och bättre felhantering
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, 
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, 
    PDO::ATTR_EMULATE_PREPARES   => false,        
];

try {
    // Skapa PDO-anslutning
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    // Visa felmeddelande om anslutningen misslyckas
    die('Databasanslutning misslyckades: ' . $e->getMessage());
}
?>
