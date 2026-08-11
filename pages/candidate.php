<?php
// ============================================
// candidate.php - Perfil do candidato
// ============================================
$candidate_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($candidate_id > 0) {
    $stmt = $pdo->prepare("SELECT u.*, cp.education, cp.experience, cp.languages, cp.resume_file 
                           FROM users u 
                           LEFT JOIN candidate_profiles cp ON u.id = cp.user_id 
                           WHERE u.id = ? AND u.user_type = 'candidate'");
    $stmt->execute([$candidate_id]);
    $candidate = $stmt->fetch(PDO::FETCH_ASSOC);
} else if (isLoggedIn() && isCandidate()) {
    $stmt = $pdo->prepare("SELECT u.*, cp.education, cp.experience, cp.languages, cp.resume_file 
                           FROM users u 
                           LEFT JOIN candidate_profiles cp ON u.id = cp.user_id 
                           WHERE u.id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $candidate = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!isset($candidate) || !$candidate) {
    echo "<p style='text-align:center;padding:40px;'>Candidato não encontrado.</p>";
    return;
}
?>

<div class="profile-container">
    <div class="profile-left">
        <div class="avatar-lg">
            <?php if (!empty($candidate['photo'])): ?>
                <img src="<?php echo htmlspecialchars($candidate['photo']); ?>" alt="Foto">
            <?php else: ?>
                <i class="fas fa-user-circle"></i>
            <?php endif; ?>
        </div>
        <h2 style="margin-top: 12px;"><?php echo htmlspecialchars($candidate['name']); ?></h2>
        <span class="sub" style="color: var(--azul-medio);">Candidato</span>
        <?php if (!empty($candidate['resume_file'])): ?>
            <a href="<?php echo htmlspecialchars($candidate['resume_file']); ?>" target="_blank" class="btn-secondary" style="margin-top: 12px; display: inline-block; padding: 8px 20px; font-size: 0.9rem;">
                <i class="fas fa-file-pdf"></i> Ver Currículo
            </a>
        <?php endif; ?>
    </div>
    <div class="profile-right">
        <h2><i class="fas fa-id-card"></i> Perfil profissional</h2>
        <div class="detail-grid">
            <div class="detail-item"><i class="fas fa-calendar-alt"></i> <?php echo htmlspecialchars($candidate['age']); ?> anos</div>
            <div class="detail-item"><i class="fas fa-map-pin"></i> <?php echo htmlspecialchars($candidate['address'] ?? 'Não informado'); ?></div>
            <div class="detail-item"><i class="fas fa-graduation-cap"></i> <?php echo htmlspecialchars($candidate['education'] ?? 'Não informado'); ?></div>
            <div class="detail-item"><i class="fas fa-id-card"></i> CPF: <?php echo substr($candidate['cpf'], 0, 3) . '.***.***-**'; ?></div>
            <div class="detail-item"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($candidate['phone'] ?? 'Não informado'); ?></div>
            <div class="detail-item"><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($candidate['email']); ?></div>
            <div class="detail-item"><i class="fas fa-language"></i> <?php echo htmlspecialchars($candidate['languages'] ?? 'Não informado'); ?></div>
            <div class="detail-item"><i class="fas fa-briefcase"></i> <?php echo htmlspecialchars($candidate['experience'] ?? '0 anos'); ?></div>
        </div>
        
        <?php if (!empty($candidate['description'])): ?>
        <div class="descricao">
            <strong><i class="fas fa-quote-left"></i> Descrição pessoal</strong>
            <p style="margin-top: 6px;"><?php echo nl2br(htmlspecialchars($candidate['description'])); ?></p>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($candidate['experience']) && strlen($candidate['experience']) > 50): ?>
        <div style="margin-top: 10px;">
            <strong>Experiência profissional detalhada:</strong>
            <p style="margin-top: 4px;"><?php echo nl2br(htmlspecialchars($candidate['experience'])); ?></p>
        </div>
        <?php endif; ?>
    </div>
</div>