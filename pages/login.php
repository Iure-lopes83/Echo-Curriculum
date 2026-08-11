<?php
// ============================================
// login.php - Formulário de login
// ============================================
$error = isset($_GET['error']) ? $_GET['error'] : '';
$registered = isset($_GET['registered']) ? $_GET['registered'] : '';
?>
<div class="auth-card" style="max-width: 480px;">
    <div style="text-align: center; margin-bottom: 24px;">
        <div class="logo-ec" style="display: inline-block; font-size: 2.5rem; padding: 8px 24px; margin-bottom: 8px;">EC</div>
        <h2 style="color: var(--azul-profundo); font-size: 1.8rem;">Bem-vindo de volta</h2>
        <p class="subtitle" style="color: #4a6a8a;">Acesse sua conta com CPF, CNPJ ou E-mail</p>
    </div>
    
    <?php if ($error): ?>
        <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <?php if ($registered): ?>
        <div class="alert success">Cadastro realizado com sucesso! Faça login.</div>
    <?php endif; ?>
    
    <form method="POST" action="index.php?page=login">
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
