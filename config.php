<?php
// ============================================
// config.php - Configurações (CORRIGIDO)
// ============================================

// Usar variáveis de ambiente do Render
$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: 'ec_curriculum';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';

try {
    // Tentar PostgreSQL primeiro
    $dsn = "pgsql:host=$host;dbname=$dbname;port=5432";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Se PostgreSQL falhar, tentar MySQL
    try {
        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8;port=3306";
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch (PDOException $e2) {
        // Em produção, não mostre o erro detalhado
        if (getenv('RENDER') === 'true') {
            die("Erro de conexão com o banco de dados. Verifique as configurações.");
        } else {
            die("Erro na conexão: " . $e2->getMessage());
        }
    }
}

// ============================================
// SESSÃO - Deve ser iniciada ANTES de qualquer saída HTML
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
    // Limpar qualquer saída em buffer
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Redirecionar
    header("Location: $url");
    exit();
}

// Função para redirecionamento com JavaScript (fallback)
function redirect_js($url) {
    echo "<script>window.location.href='$url';</script>";
    exit();
}
?>
