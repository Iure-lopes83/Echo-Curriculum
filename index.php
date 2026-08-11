<?php
// ============================================
// index.php - Página principal (SIMPLIFICADO)
// ============================================

// Inicializar o sistema (processa tudo ANTES do HTML)
require_once __DIR__ . '/init.php';

// Agora sim, incluir o header (HTML)
include __DIR__ . '/header.php';
include __DIR__ . '/menu.php';

// Conteúdo
echo '<main class="container">';
$page_file = __DIR__ . "/pages/$page.php";
if (file_exists($page_file)) {
    include $page_file;
} else {
    include __DIR__ . '/pages/home.php';
}
echo '</main>';

include __DIR__ . '/footer.php';
?>
