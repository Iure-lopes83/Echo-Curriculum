<?php
// ============================================
// logout.php - Logout (CORRIGIDO)
// ============================================

// Incluir config primeiro
require_once __DIR__ . '/../config.php';

// Limpar sessão
$_SESSION = array();
session_destroy();

// IMPORTANTE: Limpar buffer antes de redirecionar
while (ob_get_level()) {
    ob_end_clean();
}

// Redirecionar usando header
header("Location: index.php?page=home");
exit();
?>
