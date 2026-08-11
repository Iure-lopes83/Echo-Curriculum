<?php
// ============================================
// index.php - Roteador principal
// ============================================
require_once 'config.php';

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

// Incluir header
include 'header.php';

// Incluir menu
include 'menu.php';

// Conteúdo principal
echo '<main class="container">';

// Carregar página conforme rota
$page_file = "pages/$page.php";
if (file_exists($page_file)) {
    include $page_file;
} else {
    include 'pages/home.php';
}

echo '</main>';

// Incluir footer
include 'footer.php';
?>