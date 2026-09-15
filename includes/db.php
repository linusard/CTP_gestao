<?php
// Conexão principal do sistema: usada pelo calendário e por toda a gestão de cursos/turmas
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestao_cursos";

try {
    // Create a new PDO instance and set the error mode to exception
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Handle connection failure
    echo "Connection failed: " . $e->getMessage();
}
?>
