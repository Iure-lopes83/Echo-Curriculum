<?php
// ============================================
// job_detail.php - Detalhe de uma vaga
// ============================================
$job_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($job_id <= 0) {
    echo "<p style='text-align:center;padding:40px;'>Vaga não encontrada.</p>";
    return;
}

$stmt = $pdo->prepare("SELECT j.*, u.name as company_name, u.id as recruiter_id, rp.company_name as company_display, rp.company_description 
                       FROM jobs j 
                       JOIN users u ON j.recruiter_id = u.id 
                       LEFT JOIN recruiter_profiles rp ON u.id = rp.user_id 
                       WHERE j.id = ?");
$stmt->execute([$job_id]);
$job = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$job) {
    echo "<p style='text-align:center;padding:40px;'>Vaga não encontrada.</p>";
    return;
}

// Processar candidatura
$apply_message = '';
if (isset($_GET['apply']) && isLoggedIn() && isCandidate()) {
    try {
        $stmt = $pdo->prepare("SELECT id FROM applications WHERE job_id = ? AND candidate_id = ?");
        $stmt->execute([$job_id, $_SESSION['user_id']]);
        if ($stmt->fetch()) {
            $apply_message = 'Você já se candidatou a esta vaga.';
        } else {
            $stmt = $pdo->prepare("INSERT INTO applications (job_id, candidate_id) VALUES (?, ?)");
            $stmt->execute([$job_id, $_SESSION['user_id']]);
            $apply_message = 'Candidatura realizada com sucesso!';
        }
    } catch (Exception $e) {
        $apply_message = 'Erro ao se candidatar. Tente novamente.';
    }
}

// Verificar se já se candidatou
$has_applied = false;
if (isLoggedIn() && isCandidate()) {
    $stmt = $pdo->prepare("SELECT id FROM applications WHERE job_id = ? AND candidate_id = ?");
    $stmt->execute([$job_id, $_SESSION['user_id']]);
    $has_applied = $stmt->fetch() ? true : false;
}
?>

<div class="profile-container" style="display: block;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; margin-bottom: 16px;">
        <div>
            <h2 style="color: var(--azul-profundo);"><?php echo htmlspecialchars($job['title']); ?></h2>
            <div class="company" style="font-size: 1.1rem;">
                <i class="fas fa-building"></i> 
                <?php echo htmlspecialchars($job['company_display'] ?? $job['company_name']); ?>
            </div>
        </div>
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <span class="tag"><?php echo htmlspecialchars($job['job_type'] ?? 'CLT'); ?></span>
            <span class="tag" style="background: #d4edda; color: #155724;">Ativa</span>
        </div>
    </div>
    
    <div style="display: flex; flex-wrap: wrap; gap: 16px; margin: 12px 0 20px; padding: 16px; background: #f6faff; border-radius: 16px;">
        <span><i class="fas fa-map-pin" style="color: var(--azul-medio);"></i> <?php echo htmlspecialchars($job['location'] ?? 'Não informado'); ?></span>
        <span><i class="fas fa-dollar-sign" style="color: var(--azul-medio);"></i> <?php echo htmlspecialchars($job['salary_range'] ?? 'A combinar'); ?></span>
        <span><i class="fas fa-calendar-alt" style="color: var(--azul-medio);"></i> Publicada em: <?php echo date('d/m/Y', strtotime($job['created_at'])); ?></span>
    </div>
    
    <div style="margin: 16px 0;">
        <h3 style="color: var(--azul-profundo);"><i class="fas fa-align-left"></i> Descrição da vaga</h3>
        <p style="margin-top: 8px; line-height: 1.6;"><?php echo nl2br(htmlspecialchars($job['description'])); ?></p>
    </div>
    
    <?php if (!empty($job['requirements'])): ?>
    <div style="margin: 16px 0;">
        <h3 style="color: var(--azul-profundo);"><i class="fas fa-list-check"></i> Requisitos</h3>
        <div class="tag-group" style="margin-top: 8px;">
            <?php 
            $reqs = explode(',', $job['requirements']);
            foreach ($reqs as $req):
                $req = trim($req);
                if ($req):
            ?>
                <span class="tag" style="font-size: 0.85rem; padding: 4px 18px;"><?php echo htmlspecialchars($req); ?></span>
            <?php endif; endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($job['company_description'])): ?>
    <div style="margin: 16px 0; padding: 16px 20px; background: #f6faff; border-radius: 16px; border-left: 4px solid var(--azul-medio);">
        <h3 style="color: var(--azul-profundo);"><i class="fas fa-info-circle"></i> Sobre a empresa</h3>
        <p style="margin-top: 6px;"><?php echo nl2br(htmlspecialchars($job['company_description'])); ?></p>
    </div>
    <?php endif; ?>
    
    <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid #e8eff6; display: flex; gap: 16px; flex-wrap: wrap; align-items: center;">
        <?php if (isLoggedIn() && isCandidate()): ?>
            <?php if ($has_applied): ?>
                <button class="btn-apply" style="background: #28a745; cursor: default;" disabled>
                    <i class="fas fa-check"></i> Candidatura enviada
                </button>
            <?php else: ?>
                <a href="index.php?page=job_detail&id=<?php echo $job_id; ?>&apply=1" class="btn-apply">
                    <i class="fas fa-paper-plane"></i> Candidatar-se
                </a>
            <?php endif; ?>
            <?php if ($apply_message): ?>
                <span style="color: #155724; font-weight: 500;"><?php echo $apply_message; ?></span>
            <?php endif; ?>
        <?php elseif (!isLoggedIn()): ?>
            <a href="index.php?page=login" class="btn-apply">
                <i class="fas fa-sign-in-alt"></i> Faça login para se candidatar
            </a>
        <?php elseif (isLoggedIn() && isRecruiter() && $_SESSION['user_id'] == $job['recruiter_id']): ?>
            <span style="color: var(--azul-medio); font-weight: 500;"><i class="fas fa-user-tie"></i> Esta é uma vaga da sua empresa</span>
        <?php endif; ?>
        
        <a href="index.php?page=jobs" class="btn-link"><i class="fas fa-arrow-left"></i> Voltar para vagas</a>
    </div>
</div>