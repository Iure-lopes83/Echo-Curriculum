<?php
// ============================================
// login.php - Login (CORRIGIDO)
// ============================================
require_once __DIR__ . '/../config.php';

$error = '';

// Processar login ANTES de qualquer saída HTML
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $login = $_POST['login_input'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($login) || empty($password)) {
        $error = 'Preencha todos os campos.';
    } else {
        // Buscar por CPF, CNPJ ou email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE cpf = ? OR cnpj = ? OR email = ?");
        $stmt->execute([$login, $login, $login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_type'] = $user['user_type'];
            
            // Limpar buffer e redirecionar
            while (ob_get_level()) {
                ob_end_clean();
            }
            header("Location: index.php?page=home");
            exit();
        } else {
            $error = 'CPF/CNPJ/E-mail ou senha inválidos.';
        }
    }
}

// Agora sim, incluir o header (depois de todo o processamento)
// O header.php NÃO foi incluído antes do redirect
?>
<div class="auth-card" style="max-width: 480px;">
    <div style="text-align: center; margin-bottom: 24px;">
        <div class="logo-ec" style="display: inline-block; font-size: 2.5rem; padding: 8px 24px; margin-bottom: 8px;">EC</div>
        <h2 style="color: var(--azul-profundo); font-size: 1.8rem;">Bem-vindo de volta</h2>
        <p class="subtitle" style="color: #4a6a8a;">Acesse sua conta com CPF, CNPJ ou E-mail</p>
    </div>
    
    <?php if ($error): ?>
        <div class="alert error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="form-group">
            <label><i class="fas fa-id-card"></i> CPF / CNPJ / E-mail</label>
            <input type="text" name="login_input" placeholder="Digite seu CPF, CNPJ ou E-mail" required>
        </div>
        <div class="form-group">
            <label><i class="fas fa-lock"></i> Senha</label>
            <input type="password" name="password" placeholder="••••••••" required>
        </div>
        <button type="submit" name="login" class="btn-primary"><i class="fas fa-sign-in-alt"></i> Entrar</button>
    </form>
    
    <div class="auth-switch" style="margin-top: 20px;">
        Não tem conta? 
        <a href="index.php?page=register">Cadastre-se agora</a>
    </div>
</div>
