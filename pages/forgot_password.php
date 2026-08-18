<?php
// ============================================
// forgot_password.php - Recuperação de senha
// ============================================
$error = '';
$success = '';
$step = isset($_GET['step']) ? $_GET['step'] : 'request'; // request ou reset

// Processar solicitação de recuperação
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_reset'])) {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Digite um e-mail válido.';
    } else {
        try {
            // Verificar se o e-mail existe
            $stmt = $pdo->prepare("SELECT id, name FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // Gerar token único
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                // Salvar token no banco (será criada tabela se não existir)
                try {
                    $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
                    $stmt->execute([$email, $token, $expires]);
                } catch (Exception $e) {
                    // Se a tabela não existir, criar
                    $pdo->exec("CREATE TABLE IF NOT EXISTS password_resets (
                        id SERIAL PRIMARY KEY,
                        email VARCHAR(100) NOT NULL,
                        token VARCHAR(64) NOT NULL,
                        expires_at TIMESTAMP NOT NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )");
                    $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
                    $stmt->execute([$email, $token, $expires]);
                }
                
                // Em produção, enviar e-mail com link
                // Por enquanto, mostramos o link na tela
                $reset_link = "index.php?page=forgot_password&step=reset&token=" . $token . "&email=" . urlencode($email);
                $success = "Um link de recuperação foi gerado.<br>
                            <a href='$reset_link' style='color: #1c4e80; font-weight: bold;'>Clique aqui para redefinir sua senha</a>
                            <br><small style='color: #666;'>Este link expira em 1 hora.</small>";
            } else {
                // Por segurança, não informar que o e-mail não existe
                $success = "Se este e-mail estiver cadastrado, você receberá um link de recuperação.";
            }
        } catch (Exception $e) {
            $error = 'Erro ao processar solicitação. Tente novamente.';
        }
    }
}

// Processar redefinição de senha
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {
    $token = $_POST['token'] ?? '';
    $email = $_POST['email'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($token) || empty($email)) {
        $error = 'Token inválido.';
    } elseif (strlen($new_password) < 8) {
        $error = 'A nova senha deve ter pelo menos 8 caracteres.';
    } elseif ($new_password !== $confirm_password) {
        $error = 'As senhas não coincidem.';
    } else {
        try {
            // Verificar token
            $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE email = ? AND token = ? AND expires_at > NOW()");
            $stmt->execute([$email, $token]);
            $reset = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($reset) {
                // Atualizar senha
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
                $stmt->execute([$hashed_password, $email]);
                
                // Remover token usado
                $stmt = $pdo->prepare("DELETE FROM password_resets WHERE email = ?");
                $stmt->execute([$email]);
                
                $success = "Senha redefinida com sucesso!";
                $step = 'success';
            } else {
                $error = 'Token inválido ou expirado. Solicite uma nova recuperação.';
            }
        } catch (Exception $e) {
            $error = 'Erro ao redefinir senha. Tente novamente.';
        }
    }
}

// Verificar token para exibir formulário de reset
$token = isset($_GET['token']) ? $_GET['token'] : '';
$email = isset($_GET['email']) ? $_GET['email'] : '';
$valid_token = false;

if (!empty($token) && !empty($email) && $step === 'reset') {
    try {
        $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE email = ? AND token = ? AND expires_at > NOW()");
        $stmt->execute([$email, $token]);
        if ($stmt->fetch()) {
            $valid_token = true;
        } else {
            $error = 'Token inválido ou expirado.';
        }
    } catch (Exception $e) {
        $error = 'Erro ao verificar token.';
    }
}
?>

<?php if ($step === 'request' || empty($step)): ?>
<!-- Solicitar recuperação -->
<div class="auth-card" style="max-width: 480px;">
    <div style="text-align: center; margin-bottom: 24px;">
        <div class="logo-ec" style="display: inline-block; font-size: 2.5rem; padding: 8px 24px; margin-bottom: 8px;">EC</div>
        <h2 style="color: var(--azul-profundo); font-size: 1.8rem;">Recuperar Senha</h2>
        <p class="subtitle" style="color: #4a6a8a;">Digite seu e-mail para receber o link de recuperação</p>
    </div>
    
    <?php if ($error): ?>
        <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="form-group">
            <label><i class="fas fa-envelope"></i> E-mail cadastrado</label>
            <input type="email" name="email" placeholder="Digite seu e-mail" required>
        </div>
        <button type="submit" name="request_reset" class="btn-primary">
            <i class="fas fa-paper-plane"></i> Enviar link de recuperação
        </button>
    </form>
    
    <div class="auth-switch" style="margin-top: 20px;">
        <a href="index.php?page=login">Voltar para o login</a>
    </div>
</div>

<?php elseif ($step === 'reset' && $valid_token): ?>
<!-- Redefinir senha -->
<div class="auth-card" style="max-width: 480px;">
    <div style="text-align: center; margin-bottom: 24px;">
        <div class="logo-ec" style="display: inline-block; font-size: 2.5rem; padding: 8px 24px; margin-bottom: 8px;">EC</div>
        <h2 style="color: var(--azul-profundo); font-size: 1.8rem;">Redefinir Senha</h2>
        <p class="subtitle" style="color: #4a6a8a;">Digite sua nova senha</p>
    </div>
    
    <?php if ($error): ?>
        <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
        
        <div class="form-group">
            <label><i class="fas fa-lock"></i> Nova senha (mín. 8 caracteres)</label>
            <input type="password" name="new_password" placeholder="Digite a nova senha" required>
        </div>
        <div class="form-group">
            <label><i class="fas fa-check-circle"></i> Confirmar senha</label>
            <input type="password" name="confirm_password" placeholder="Confirme a nova senha" required>
        </div>
        <button type="submit" name="reset_password" class="btn-primary">
            <i class="fas fa-save"></i> Redefinir Senha
        </button>
    </form>
    
    <div class="auth-switch" style="margin-top: 20px;">
        <a href="index.php?page=login">Voltar para o login</a>
    </div>
</div>

<?php elseif ($step === 'success'): ?>
<!-- Sucesso -->
<div class="auth-card" style="max-width: 480px; text-align: center;">
    <div style="font-size: 4rem; color: #28a745; margin-bottom: 16px;">
        <i class="fas fa-check-circle"></i>
    </div>
    <h2 style="color: var(--azul-profundo); font-size: 1.8rem;">✅ Senha Redefinida!</h2>
    <p style="color: #4a6a8a; margin-bottom: 20px;">Sua senha foi alterada com sucesso. Agora você pode fazer login.</p>
    <a href="index.php?page=login" class="btn-primary" style="display: inline-block; width: auto; padding: 12px 40px; text-decoration: none;">
        <i class="fas fa-sign-in-alt"></i> Ir para Login
    </a>
</div>

<?php else: ?>
<!-- Token inválido -->
<div class="auth-card" style="max-width: 480px; text-align: center;">
    <div style="font-size: 4rem; color: #dc3545; margin-bottom: 16px;">
        <i class="fas fa-exclamation-circle"></i>
    </div>
    <h2 style="color: var(--azul-profundo); font-size: 1.8rem;">❌ Link Inválido</h2>
    <p style="color: #4a6a8a; margin-bottom: 20px;">O link de recuperação é inválido ou expirou. Solicite um novo.</p>
    <a href="index.php?page=forgot_password" class="btn-primary" style="display: inline-block; width: auto; padding: 12px 40px; text-decoration: none;">
        <i class="fas fa-redo"></i> Solicitar novo link
    </a>
</div>
<?php endif; ?>
