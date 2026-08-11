<?php
// ============================================
// jobs.php - Lista de vagas disponíveis
// ============================================
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$search = isset($_GET['q']) ? $_GET['q'] : '';

$sql = "SELECT j.*, u.name as company_name, rp.company_name as company_display 
        FROM jobs j 
        JOIN users u ON j.recruiter_id = u.id 
        LEFT JOIN recruiter_profiles rp ON u.id = rp.user_id 
        WHERE j.status = 'active'";

$params = [];

if (!empty($search)) {
    $sql .= " AND (j.title LIKE ? OR j.description LIKE ? OR j.requirements LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($filter === 'recent') {
    $sql .= " ORDER BY j.created_at DESC";
} else if ($filter === 'salary') {
    $sql .= " ORDER BY CAST(REPLACE(REPLACE(j.salary_range, 'R$ ', ''), '.', '') AS UNSIGNED) DESC";
} else {
    $sql .= " ORDER BY j.created_at DESC";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="section-title" style="margin-top: 20px;">
    <i class="fas fa-briefcase"></i> Vagas Disponíveis
</div>

<div style="display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 20px; align-items: center;">
    <form method="GET" action="index.php" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center; flex: 1;">
        <input type="hidden" name="page" value="jobs">
        <input type="text" name="q" placeholder="Buscar vagas..." value="<?php echo htmlspecialchars($search); ?>" style="flex: 1; min-width: 200px; padding: 10px 16px; border: 1px solid #ccdcee; border-radius: 40px;">
        <button type="submit" class="btn-secondary" style="padding: 10px 24px;"><i class="fas fa-search"></i> Buscar</button>
    </form>
    <div style="display: flex; gap: 8px;">
        <a href="index.php?page=jobs&filter=recent" class="btn-link <?php echo $filter === 'recent' ? 'active' : ''; ?>">Recent</a>
        <a href="index.php?page=jobs&filter=salary" class="btn-link <?php echo $filter === 'salary' ? 'active' : ''; ?>">Salário</a>
        <a href="index.php?page=jobs&filter=all" class="btn-link <?php echo $filter === 'all' ? 'active' : ''; ?>">Todas</a>
    </div>
</div>

<?php if (empty($jobs)): ?>
    <p style="text-align: center; padding: 40px; color: #555;">Nenhuma vaga disponível no momento.</p>
<?php else: ?>
    <?php foreach ($jobs as $j): ?>
    <div class="job-card">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap;">
            <div>
                <h3><?php echo htmlspecialchars($j['title']); ?></h3>
                <div class="company">
                    <i class="fas fa-building"></i> 
                    <?php echo htmlspecialchars($j['company_display'] ?? $j['company_name']); ?>
                </div>
            </div>
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                <span class="tag"><?php echo htmlspecialchars($j['job_type'] ?? 'CLT'); ?></span>
                <?php if ($j['status'] === 'active'): ?>
                    <span class="tag" style="background: #d4edda; color: #155724;">Ativa</span>
                <?php endif; ?>
            </div>
        </div>
        <div class="job-meta">
            <span><i class="fas fa-map-pin"></i> <?php echo htmlspecialchars($j['location'] ?? 'Não informado'); ?></span>
            <span><i class="fas fa-dollar-sign"></i> <?php echo htmlspecialchars($j['salary_range'] ?? 'A combinar'); ?></span>
            <span><i class="fas fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($j['created_at'])); ?></span>
        </div>
        <p style="margin: 8px 0; color: #444;"><?php echo substr(htmlspecialchars($j['description']), 0, 200) . '...'; ?></p>
        <?php if (!empty($j['requirements'])): ?>
            <div style="margin: 6px 0;">
                <strong>Requisitos:</strong>
                <div class="tag-group">
                    <?php 
                    $reqs = explode(',', $j['requirements']);
                    foreach ($reqs as $req):
                        $req = trim($req);
                        if ($req):
                    ?>
                        <span class="tag"><?php echo htmlspecialchars($req); ?></span>
                    <?php endif; endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        <div style="margin-top: 12px; display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="index.php?page=job_detail&id=<?php echo $j['id']; ?>" class="btn-link">Ver detalhes <i class="fas fa-arrow-right"></i></a>
            <?php if (isLoggedIn() && isCandidate()): ?>
                <a href="index.php?page=job_detail&id=<?php echo $j['id']; ?>&apply=1" class="btn-apply" style="padding: 8px 24px; font-size: 0.9rem;">Candidatar-se</a>
            <?php elseif (!isLoggedIn()): ?>
                <a href="index.php?page=login" class="btn-apply" style="padding: 8px 24px; font-size: 0.9rem;">Faça login para se candidatar</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>