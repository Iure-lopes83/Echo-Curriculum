<?php
// ============================================
// profile.php - Editar perfil
// ============================================
require_once __DIR__ . '/../config.php';

if (!isLoggedIn()) {
    redirect('index.php?page=login');
}

$user_id = $_SESSION['user_id'];
$is_recruiter = isRecruiter();

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($is_recruiter) {
    $stmt = $pdo->prepare("SELECT * FROM recruiter_profiles WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $stmt = $pdo->prepare("SELECT * FROM candidate_profiles WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $name = $_POST['name'] ?? $user['name'];
    $age = intval($_POST['age'] ?? $user['age']);
    $email = $_POST['email'] ?? $user['email'];
    $phone = $_POST['phone'] ?? $user['phone'];
    $address = $_POST['address'] ?? $user['address'];
    $photo = $_POST['photo'] ?? $user['photo'];
    $description = $_POST['description'] ?? $user['description'];
    
    try {
        $stmt = $pdo->prepare("UPDATE users SET name = ?, age = ?, email = ?, phone = ?, address = ?, photo = ?, description = ? WHERE id = ?");
        $stmt->execute([$name, $age, $email, $phone, $address, $photo, $description, $user_id]);
        
        if (!$is_recruiter) {
            $education = $_POST['education'] ?? '';
            $experience = $_POST['experience'] ?? '';
            $languages = $_POST['languages'] ?? '';
            
            if ($profile) {
                $stmt = $pdo->prepare("UPDATE candidate_profiles SET education = ?, experience = ?, languages = ? WHERE user_id = ?");
                $stmt->execute([$education, $experience, $languages, $user_id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO candidate_profiles (user_id, education, experience, languages) VALUES (?, ?, ?, ?)");
                $stmt->execute([$user_id, $education, $experience, $languages]);
            }
        } else {
            $company_name = $_POST['company_name'] ?? '';
            $company_description = $_POST['company_description'] ?? '';
            
            if ($profile) {
                $stmt = $pdo->prepare("UPDATE recruiter_profiles SET company_name = ?, company_description = ? WHERE user_id = ?");
                $stmt->execute([$company_name, $company_description, $user_id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO recruiter_profiles (user_id, company_name, company_description) VALUES (?, ?, ?)");
                $stmt->execute([$user_id, $company_name, $company_description]);
            }
        }
        
        $success = 'Perfil atualizado com sucesso!';
        // Recarregar dados
        header("Refresh:0");
    } catch (Exception $e) {
        $error = 'Erro ao atualizar: ' . $e->getMessage();
    }
}
?>

<div class="auth-card" style="max-width: 700px;">
    <h2><i class="fas fa-user-edit"></i> Editar Perfil</h2>
    <div class="subtitle">Atualize suas informações pessoais</div>
    
    <?php if ($success): ?>
        <div class="alert success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <label>Nome completo</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>
            <div class="form-group">
                <label>Idade</label>
                <input type="number" name="age" min="18" value="<?php echo htmlspecialchars($user['age']); ?>" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>E-mail</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="form-group">
                <label>Telefone</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
            </div>
        </div>
        
        <?php if (!$is_recruiter): ?>
            <div class="form-group">
                <label>Formação acadêmica</label>
                <input type="text" name="education" value="<?php echo htmlspecialchars($profile['education'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Experiência profissional</label>
                <textarea name="experience"><?php echo htmlspecialchars($profile['experience'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label>Idiomas</label>
                <input type="text" name="languages" value="<?php echo htmlspecialchars($profile['languages'] ?? ''); ?>">
            </div>
        <?php else: ?>
            <div class="form-group">
                <label>Nome da empresa</label>
                <input type="text" name="company_name" value="<?php echo htmlspecialchars($profile['company_name'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Descrição da empresa</label>
                <textarea name="company_description"><?php echo htmlspecialchars($profile['company_description'] ?? ''); ?></textarea>
            </div>
        <?php endif; ?>
        
        <div class="form-group">
            <label>Foto (URL)</label>
            <input type="text" name="photo" value="<?php echo htmlspecialchars($user['photo'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label>Descrição pessoal</label>
            <textarea name="description"><?php echo htmlspecialchars($user['description'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label>Endereço</label>
            <input type="text" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>">
        </div>

        <button type="submit" name="update" class="btn-primary"><i class="fas fa-save"></i> Atualizar perfil</button>
    </form>
</div>