<?php
// ============================================
// home.php - Página inicial
// ============================================
?>
<div class="hero">
    <h1><span class="highlight">EC</span> · Echo Curriculum</h1>
    <p>Conecte talentos e empresas com segurança. Sistema ATS integrado para gestão de recrutamento.</p>
    <form method="GET" action="index.php" class="search-bar">
        <input type="hidden" name="page" value="search">
        <input type="text" name="q" placeholder="Pesquisar por cargo, habilidade, localização...">
        <button type="submit"><i class="fas fa-search"></i> Buscar</button>
    </form>
</div>

<div class="section-title">
    <i class="fas fa-users" style="color: var(--azul-medio);"></i> Candidatos em destaque
</div>

<div class="card-grid">
    <?php
    // Buscar candidatos do banco
    try {
        $stmt = $pdo->query("SELECT u.id, u.name, u.photo, u.address, cp.experience, cp.languages 
                              FROM users u 
                              LEFT JOIN candidate_profiles cp ON u.id = cp.user_id 
                              WHERE u.user_type = 'candidate' 
                              ORDER BY u.created_at DESC LIMIT 6");
        $profiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $profiles = [];
    }
    
    if (empty($profiles)) {
        // Dados de exemplo
        $profiles = [
            ['id' => 1, 'name' => 'Ana Beatriz Souza', 'photo' => '', 'address' => 'São Paulo, SP', 'experience' => 'Engenheira de Software Sênior', 'languages' => 'Inglês, Espanhol'],
            ['id' => 2, 'name' => 'Carlos Eduardo Lima', 'photo' => '', 'address' => 'Rio de Janeiro, RJ', 'experience' => 'Product Designer', 'languages' => 'Espanhol'],
            ['id' => 3, 'name' => 'Mariana Fernandes', 'photo' => '', 'address' => 'Belo Horizonte, MG', 'experience' => 'Cientista de Dados', 'languages' => 'Inglês'],
            ['id' => 4, 'name' => 'Rafael Oliveira', 'photo' => '', 'address' => 'Curitiba, PR', 'experience' => 'DevOps Engineer', 'languages' => 'Inglês'],
            ['id' => 5, 'name' => 'Fernanda Rocha', 'photo' => '', 'address' => 'Porto Alegre, RS', 'experience' => 'UX/UI Designer', 'languages' => 'Inglês, Francês'],
            ['id' => 6, 'name' => 'Thiago Mendes', 'photo' => '', 'address' => 'Brasília, DF', 'experience' => 'Backend Developer', 'languages' => 'Inglês']
        ];
    }
    
    foreach ($profiles as $p):
    ?>
    <div class="card-perfil">
        <div class="card-foto">
            <?php if (!empty($p['photo'])): ?>
                <img src="<?php echo htmlspecialchars($p['photo']); ?>" alt="Foto">
            <?php else: ?>
                <i class="fas fa-user-circle"></i>
            <?php endif; ?>
        </div>
        <h3><?php echo htmlspecialchars($p['name']); ?></h3>
        <div class="cargo"><?php echo htmlspecialchars($p['experience'] ?? 'Profissional'); ?></div>
        <div class="info-item"><i class="fas fa-map-pin"></i> <?php echo htmlspecialchars($p['address'] ?? 'Não informado'); ?></div>
        <div class="tag-group">
            <?php 
            $langs = explode(',', $p['languages'] ?? '');
            foreach ($langs as $lang):
                $lang = trim($lang);
                if ($lang):
            ?>
                <span class="tag"><?php echo htmlspecialchars($lang); ?></span>
            <?php endif; endforeach; ?>
        </div>
        <a href="index.php?page=candidate&id=<?php echo $p['id']; ?>" class="btn-link">Ver perfil <i class="fas fa-arrow-right"></i></a>
    </div>
    <?php endforeach; ?>
</div>

<div class="section-title">
    <i class="fas fa-briefcase" style="color: var(--azul-medio);"></i> Vagas em destaque
</div>

<div class="card-grid">
    <?php
    try {
        $stmt = $pdo->query("SELECT j.*, u.name as company_name 
                              FROM jobs j 
                              JOIN users u ON j.recruiter_id = u.id 
                              WHERE j.status = 'active' 
                              ORDER BY j.created_at DESC LIMIT 4");
        $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $jobs = [];
    }
    
    if (empty($jobs)) {
        $jobs = [
            ['id' => 1, 'title' => 'Desenvolvedor Full Stack', 'company_name' => 'TechNova Solutions', 'location' => 'São Paulo, SP', 'job_type' => 'CLT', 'salary_range' => 'R$ 8.000 - R$ 12.000'],
            ['id' => 2, 'title' => 'Analista de Dados', 'company_name' => 'DataMind', 'location' => 'Remoto', 'job_type' => 'PJ', 'salary_range' => 'R$ 6.000 - R$ 9.000'],
            ['id' => 3, 'title' => 'UX/UI Designer', 'company_name' => 'Creative Lab', 'location' => 'Rio de Janeiro, RJ', 'job_type' => 'CLT', 'salary_range' => 'R$ 5.500 - R$ 8.000'],
            ['id' => 4, 'title' => 'DevOps Engineer', 'company_name' => 'Cloud Solutions', 'location' => 'Remoto', 'job_type' => 'CLT', 'salary_range' => 'R$ 9.000 - R$ 14.000']
        ];
    }
    
    foreach ($jobs as $j):
    ?>
    <div class="job-card">
        <h3><?php echo htmlspecialchars($j['title']); ?></h3>
        <div class="company"><i class="fas fa-building"></i> <?php echo htmlspecialchars($j['company_name']); ?></div>
        <div class="job-meta">
            <span><i class="fas fa-map-pin"></i> <?php echo htmlspecialchars($j['location'] ?? 'Não informado'); ?></span>
            <span><i class="fas fa-briefcase"></i> <?php echo htmlspecialchars($j['job_type'] ?? 'CLT'); ?></span>
            <span><i class="fas fa-dollar-sign"></i> <?php echo htmlspecialchars($j['salary_range'] ?? 'A combinar'); ?></span>
        </div>
        <a href="index.php?page=job_detail&id=<?php echo $j['id']; ?>" class="btn-link">Ver vaga <i class="fas fa-arrow-right"></i></a>
    </div>
    <?php endforeach; ?>
</div>