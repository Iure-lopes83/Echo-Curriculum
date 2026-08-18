<?php
// ============================================
// init.php - Inicialização do sistema
// ============================================

// Carregar configurações
require_once __DIR__ . '/config.php';

// Definir página atual
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$allowed_pages = [
    'home', 'login', 'register', 'search', 
    'candidate', 'recruiter', 'jobs', 'job_detail',
    'post_job', 'upload_resume', 'profile', 'logout',
    'forgot_password'  // <-- ADICIONADO
];

if (!in_array($page, $allowed_pages)) {
    $page = 'home';
}

// ... resto do código permanece igual ...
?>
