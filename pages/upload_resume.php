<?php
// ============================================
// upload_resume.php - Enviar currículo
// ============================================
if (!isLoggedIn() || !isCandidate()) {
    redirect('index.php?page=login');
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_resume'])) {
    $education = $_POST['education'] ?? '';
    $experience = $_POST['experience'] ?? '';
    $languages = $_POST['languages'] ?? '';
    
    try {
        // Atualizar perfil do candidato
        $stmt = $pdo->prepare("UPDATE candidate_profiles 
                               SET education = ?, experience = ?, languages = ? 
                               WHERE user_id = ?");
        $stmt->execute([$education, $experience, $languages, $_SESSION['user_id']]);
        
        // Se houver upload de arquivo
        if (isset($_FILES['resume_file']) && $_FILES['resume_file']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_name = time() . '_' . $_SESSION['user_id'] . '_' . basename($_FILES['resume_file']['name']);
            $file_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['resume_file']['tmp_name'], $file_path)) {
                $stmt = $pdo->prepare("UPDATE candidate_profiles SET resume_file = ? WHERE user_id = ?");
                $stmt->execute([$file_path, $_SESSION['user_id']]);
                $message = 'Currículo atualizado com sucesso!';
            } else {
                $message = 'Erro ao fazer upload do arquivo.';
            }
        } else {
            $message = 'Perfil atualizado com sucesso!';
        }
    } catch (Exception $e) {
        $message = 'Erro ao atualizar: ' . $e->getMessage();
    }
}

// Buscar dados atuais
$stmt = $pdo->prepare("SELECT u.*, cp.education, cp.experience, cp.languages, cp.resume_file 
                       FROM users u 
                       LEFT JOIN candidate_profiles cp ON u.id = cp.user_id 
                       WHERE u.id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="auth-card" style="max-width: 700px;">
    <h2><i class="fas fa-upload"></i> Enviar Currículo</h2>
    <div class="subtitle">Atualize suas informações e faça o upload do seu currículo</div>
    
    <?php if ($message): ?>
        <div class="alert <?php echo strpos($message, 'sucesso') !== false ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($user['resume_file'])): ?>
        <div style="background: #e8f0fe; padding: 12px 16px; border-radius: 12px; margin-bottom: 16px;">
            <i class="fas fa-file-pdf" style="color: #c00;"></i> 
            Currículo atual: <a href="<?php echo htmlspecialchars($user['resume_file']); ?>" target="_blank" style="color: var(--azul-medio);">
                <?php echo basename($user['resume_file']); ?>
            </a>
        </div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Formação acadêmica</label>
            <input type="text" name="education" placeholder="Ex: Bacharel em Ciência da Computação" 
                   value="<?php echo htmlspecialchars($user['education'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label>Experiência profissional</label>
            <textarea name="experience" placeholder="Descreva suas experiências, cargos e empresas..." rows="4"><?php echo htmlspecialchars($user['experience'] ?? ''); ?></textarea>
        </div>
        
        <div class="form-group">
            <label>Idiomas</label>
            <input type="text" name="languages" placeholder="Inglês (fluente), Espanhol (intermediário)" 
                   value="<?php echo htmlspecialchars($user['languages'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label>Arquivo do currículo (PDF, DOC, DOCX)</label>
            <input type="file" name="resume_file" accept=".pdf,.doc,.docx">
            <small style="color: #666;">Max: 5MB</small>
        </div>
        
        <button type="submit" name="upload_resume" class="btn-primary"><i class="fas fa-save"></i> Salvar Currículo</button>
    </form>
</div>