<?php
// ============================================
// CONFIGURAÇÕES DO BANCO DE DADOS
// ============================================
$host = 'localhost';
$dbname = 'ec_curriculum';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}

// ============================================
// SESSÃO
// ============================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isRecruiter() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'recruiter';
}

function isCandidate() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'candidate';
}

function redirect($url) {
    header("Location: $url");
    exit();
}
?>