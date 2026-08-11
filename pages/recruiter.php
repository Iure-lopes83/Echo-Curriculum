<?php
// ============================================
// recruiter.php - Perfil do recrutador
// ============================================
require_once __DIR__ . '/../config.php';

if (!isLoggedIn() || !isRecruiter()) {
    redirect('index.php?page=login');
}

// Buscar dados do recrutador
$stmt = $pdo->prepare("SELECT u.*, rp.company_name, rp.company_description 
                       FROM users u 
                       LEFT JOIN recruiter_profiles rp ON u.id = rp.user_id 
                       WHERE u.id = ?");
$stmt->execute([$_SESSION['user_id']]);
$recruiter = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$recruiter) {
    echo "<p style='text-align:center;padding:40px;'>Perfil não encontrado.</p>";
    return;
}

// Buscar vagas da empresa
$stmt = $pdo->prepare("SELECT * FROM jobs WHERE recruiter_id = ? AND status = 'active' ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="profile-container">
    <div class="profile-left">
        <div class="avatar-lg">
            <?php if (!empty($recruiter['photo'])): ?>
                <img src="<?php echo htmlspecialchars($recruiter['photo']); ?>" alt="Foto">
            <?php else: ?>
                <i class="fas fa-building" style="font-size: 3.6rem;"></i>
            <?php endif; ?>
        </div>
        <h2 style="margin-top: 12px;"><?php echo htmlspecialchars($recruiter['company_name'] ?? 'Empresa'); ?></h2>
        <span class="sub">Recrutador: <?php echo htmlspecialchars($recruiter['name']); ?></span>
        <div class="tag-group" style="justify-content: center;">
            <span class="tag">Empresa</span>
            <span class="tag">Recrutador</span>
        </div>
    </div>
    <div class="profile-right">
        <h2><i class="fas fa-briefcase"></i> Perfil da Empresa</h2>
        <div class="detail-grid">
            <div class="detail-item"><i class="fas fa-building"></i> <?php echo htmlspecialchars($recruiter['company_name'] ?? 'Não informado'); ?></div>
            <div class="detail-item"><i class="fas fa-id-card"></i> CNPJ: <?php echo !empty($recruiter['cnpj']) ? substr($recruiter['cnpj'], 0, 3) . '.***.***/****-**' : 'Não informado'; ?></div>
            <div class="detail-item"><i class="fas fa-map-pin"></i> <?php echo htmlspecialchars($recruiter['address'] ?? 'Não informado'); ?></div>
            <div class="detail-item"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($recruiter['phone'] ?? 'Não informado'); ?></div>
            <div class="detail-item"><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($recruiter['email']); ?></div>
            <div class="detail-item"><i class="fas fa-briefcase"></i> <?php echo count($jobs); ?> vagas ativas</div>
        </div>
        
        <?php if (!empty($recruiter['company_description'])): ?>
        <div class="descricao">
            <strong><i class="fas fa-quote-left"></i> Sobre a empresa</strong>
            <p style="margin-top: 6px;"><?php echo nl2br(htmlspecialchars($recruiter['company_description'])); ?></p>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($recruiter['description'])): ?>
        <div class="descricao" style="border-left-color: #6c757d;">
            <strong><i class="fas fa-user-tie"></i> Sobre o responsável</strong>
            <p style="margin-top: 6px;"><?php echo nl2br(htmlspecialchars($recruiter['description'])); ?></p>
        </div>
        <?php endif; ?>
        
        <div style="margin-top: 12px;">
            <i class="fas fa-check-circle" style="color: var(--azul-medio);"></i> 
            ATS integrado · banco de currículos seguro
        </div>
        
        <?php if (!empty($jobs)): ?>
        <div style="margin-top: 20px;">
            <h3><i class="fas fa-list"></i> Vagas publicadas</h3>
            <?php foreach ($jobs as $j): ?>
            <div style="background: #f6faff; padding: 12px 16px; border-radius: 12px; margin: 8px 0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
                <span><strong><?php echo htmlspecialchars($j['title']); ?></strong></span>
                <span style="color: #4a6a8a; font-size: 0.9rem;">
                    <i class="fas fa-map-pin"></i> <?php echo htmlspecialchars($j['location'] ?? 'Remoto'); ?>
                </span>
                <a href="index.php?page=job_detail&id=<?php echo $j['id']; ?>" class="btn-link" style="margin-top: 0;">Ver</a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.profile-container {
    background: white;
    border-radius: 28px;
    padding: 30px 34px;
    box-shadow: var(--sombra);
    margin: 20px 0 40px;
    border: 1px solid #e8eff6;
    display: flex;
    flex-wrap: wrap;
    gap: 32px;
}

.profile-left {
    flex: 1 1 240px;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.profile-left .avatar-lg {
    width: 130px;
    height: 130px;
    border-radius: 50%;
    background: var(--azul-claro);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 4rem;
    color: var(--azul-medio);
    border: 3px solid white;
    box-shadow: 0 4px 12px rgba(0,0,0,0.02);
    overflow: hidden;
}

.profile-left .avatar-lg img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-right {
    flex: 3 1 400px;
}

.profile-right h2 {
    font-size: 1.8rem;
    color: var(--azul-profundo);
}

.profile-right .sub {
    color: #2d4e72;
    margin-bottom: 12px;
    font-weight: 500;
}

.detail-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px 24px;
    margin: 16px 0;
}

.detail-item {
    display: flex;
    align-items: baseline;
    gap: 8px;
    font-size: 0.95rem;
}

.detail-item i {
    width: 22px;
    color: var(--azul-medio);
}

.descricao {
    background: #f6faff;
    padding: 16px 20px;
    border-radius: 16px;
    margin: 14px 0;
    border-left: 4px solid var(--azul-medio);
}

.tag-group {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin: 10px 0 4px;
}

.tag {
    background: #eaf1fa;
    padding: 2px 14px;
    border-radius: 40px;
    font-size: 0.7rem;
    font-weight: 600;
    color: #134074;
    letter-spacing: 0.3px;
}

.btn-link {
    color: var(--azul-medio);
    font-weight: 600;
    text-decoration: none;
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-link:hover {
    color: var(--azul-profundo);
}

@media (max-width: 700px) {
    .profile-container {
        padding: 20px;
    }
    .detail-grid {
        grid-template-columns: 1fr;
    }
}
</style>