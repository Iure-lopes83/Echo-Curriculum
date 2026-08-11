<?php
// ============================================
// init.php - Inicialização do sistema (SEM HTML)
// ============================================

// Carregar configurações
require_once __DIR__ . '/config.php';

// Definir página atual
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$allowed_pages = [
    'home', 'login', 'register', 'search', 
    'candidate', 'recruiter', 'jobs', 'job_detail',
    'post_job', 'upload_resume', 'profile', 'logout'
];

if (!in_array($page, $allowed_pages)) {
    $page = 'home';
}

// Processar ações específicas ANTES do HTML
if ($page === 'logout') {
    $_SESSION = array();
    session_destroy();
    header("Location: index.php?page=home");
    exit();
}

if ($page === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $login = $_POST['login_input'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (!empty($login) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE cpf = ? OR cnpj = ? OR email = ?");
        $stmt->execute([$login, $login, $login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_type'] = $user['user_type'];
            header("Location: index.php?page=home");
            exit();
        }
    }
}

if ($page === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    // Processar cadastro aqui...
    // (código do cadastro)
}
?>
