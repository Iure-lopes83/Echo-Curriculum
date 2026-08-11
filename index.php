<?php
// ============================================
// index.php - Roteador principal (CORRIGIDO)
// ============================================

// IMPORTANTE: config.php deve ser o PRIMEIRO a ser carregado
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

// Depois de configurar tudo, incluir o header (que tem HTML)
include __DIR__ . '/header.php';

// Incluir menu
include __DIR__ . '/menu.php';

// Conteúdo principal
echo '<main class="container">';

// Carregar página conforme rota
$page_file = __DIR__ . "/pages/$page.php";
if (file_exists($page_file)) {
    include $page_file;
} else {
    include __DIR__ . '/pages/home.php';
}

echo '</main>';

// Incluir footer
include __DIR__ . '/footer.php';
?>
