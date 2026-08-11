<?php
// ============================================
// post_job.php - Publicar nova vaga
// ============================================
require_once __DIR__ . '/../config.php';

if (!isLoggedIn() || !isRecruiter()) {
    redirect('index.php?page=login');
}

$error = '';
$success = '';

// Verificar se o recrutador tem perfil de empresa
$stmt = $pdo->prepare("SELECT id FROM recruiter_profiles WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
if (!$stmt->fetch()) {
    $error = 'Complete seu perfil de empresa antes de publicar vagas.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_job'])) {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $requirements = $_POST['requirements'] ?? '';
    $location = $_POST['location'] ?? '';
    $salary_range = $_POST['salary_range'] ?? '';
    $job_type = $_POST['job_type'] ?? 'CLT';
    
    if (empty($title) || empty($description)) {
        $error = 'Título e descrição são obrigatórios.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO jobs (recruiter_id, title, description, requirements, location, salary_range, job_type) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $title, $description, $requirements, $location, $salary_range, $job_type]);
            $success = 'Vaga publicada com sucesso!';
        } catch (Exception $e) {
            $error = 'Erro ao publicar vaga: ' . $e->getMessage();
        }
    }
}
?>

<div class="auth-card" style="max-width: 700px;">
    <h2><i class="fas fa-plus-circle"></i> Publicar Nova Vaga</h2>
    <div class="subtitle">Preencha os dados da vaga que deseja divulgar</div>
    
    <?php if ($error): ?>
        <div class="alert error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="form-group">
            <label>Título da vaga *</label>
            <input type="text" name="title" placeholder="Ex: Desenvolvedor Full Stack Sênior" required>
        </div>
        
        <div class="form-group">
            <label>Descrição da vaga *</label>
            <textarea name="description" placeholder="Descreva as atividades, responsabilidades e detalhes da vaga..." rows="5" required></textarea>
        </div>
        
        <div class="form-group">
            <label>Requisitos (separados por vírgula)</label>
            <input type="text" name="requirements" placeholder="Ex: Java, Spring Boot, AWS, Inglês avançado">
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Localização</label>
                <input type="text" name="location" placeholder="Cidade, estado ou Remoto">
            </div>
            <div class="form-group">
                <label>Faixa salarial</label>
                <input type="text" name="salary_range" placeholder="Ex: R$ 8.000 - R$ 12.000">
            </div>
        </div>
        
        <div class="form-group">
            <label>Tipo de contrato</label>
            <select name="job_type">
                <option value="CLT">CLT</option>
                <option value="PJ">PJ</option>
                <option value="Freelance">Freelance</option>
                <option value="Estágio">Estágio</option>
            </select>
        </div>
        
        <button type="submit" name="post_job" class="btn-primary"><i class="fas fa-save"></i> Publicar Vaga</button>
    </form>
</div>

<style>
.alert.error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
    padding: 12px 18px;
    border-radius: 12px;
    margin-bottom: 16px;
}

.alert.success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
    padding: 12px 18px;
    border-radius: 12px;
    margin-bottom: 16px;
}

.form-group {
    margin-bottom: 18px;
}

.form-group label {
    display: block;
    font-weight: 600;
    font-size: 0.85rem;
    margin-bottom: 4px;
    color: #1d3557;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #ccdcee;
    border-radius: 40px;
    font-size: 0.95rem;
    background: #fafdff;
    transition: 0.2s;
}

.form-group textarea {
    border-radius: 20px;
    resize: vertical;
    min-height: 70px;
}

.form-row {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
}

.form-row .form-group {
    flex: 1 1 160px;
}

.btn-primary {
    background: var(--azul-profundo);
    color: white;
    border: none;
    padding: 14px 28px;
    border-radius: 60px;
    font-weight: 700;
    font-size: 1rem;
    width: 100%;
    cursor: pointer;
    transition: 0.2s;
}

.btn-primary:hover {
    background: #0f3157;
}
</style>