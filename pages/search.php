<?php
// ============================================
// search.php - Pesquisa com filtros
// ============================================
$search = isset($_GET['q']) ? $_GET['q'] : '';
$location = isset($_GET['location']) ? $_GET['location'] : '';
$language = isset($_GET['language']) ? $_GET['language'] : '';
$education = isset($_GET['education']) ? $_GET['education'] : '';
$search_type = isset($_GET['search_type']) ? $_GET['search_type'] : 'candidates';

$results = [];
$count = 0;

if ($search_type === 'candidates') {
    $sql = "SELECT u.id, u.name, u.photo, u.address, cp.experience, cp.languages, cp.education 
            FROM users u 
            LEFT JOIN candidate_profiles cp ON u.id = cp.user_id 
            WHERE u.user_type = 'candidate'";
    $params = [];
    
    if (!empty($search)) {
        $sql .= " AND (u.name LIKE ? OR cp.experience LIKE ? OR cp.languages LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    if (!empty($location)) {
        $sql .= " AND u.address LIKE ?";
        $params[] = "%$location%";
    }
    if (!empty($language)) {
        $sql .= " AND cp.languages LIKE ?";
        $params[] = "%$language%";
    }
    if (!empty($education)) {
        $sql .= " AND cp.education LIKE ?";
        $params[] = "%$education%";
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $count = count($results);
} else {
    // Buscar vagas
    $sql = "SELECT j.*, u.name as company_name 
            FROM jobs j 
            JOIN users u ON j.recruiter_id = u.id 
            WHERE j.status = 'active'";
    $params = [];
    
    if (!empty($search)) {
        $sql .= " AND (j.title LIKE ? OR j.description LIKE ? OR j.requirements LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    if (!empty($location)) {
        $sql .= " AND j.location LIKE ?";
        $params[] = "%$location%";
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $count = count($results);
}
?>

<h2 style="margin: 24px 0 4px; color: var(--azul-profundo);">
    <i class="fas fa-search"></i> Pesquisa
</h2>

<div class="filter-panel">
    <form method="GET" action="index.php">
        <input type="hidden" name="page" value="search">
        
        <div class="filter-group">
            <label>Tipo de busca</label>
            <select name="search_type">
                <option value="candidates" <?php echo $search_type === 'candidates' ? 'selected' : ''; ?>>Candidatos</option>
                <option value="jobs" <?php echo $search_type === 'jobs' ? 'selected' : ''; ?>>Vagas</option>
            </select>
        </div>
        
        <div class="filter-group">
            <label><i class="fas fa-search"></i> Palavra-chave</label>
            <input type="text" name="q" placeholder="Cargo, habilidade..." value="<?php echo htmlspecialchars($search); ?>">
        </div>
        
        <div class="filter-group">
            <label><i class="fas fa-map-marker-alt"></i> Localização</label>
            <input type="text" name="location" placeholder="Cidade, estado" value="<?php echo htmlspecialchars($location); ?>">
        </div>
        
        <div class="filter-group" id="languageFilter">
            <label><i class="fas fa-language"></i> Idioma</label>
            <select name="language">
                <option value="">Todos</option>
                <option value="Inglês" <?php echo $language === 'Inglês' ? 'selected' : ''; ?>>Inglês</option>
                <option value="Espanhol" <?php echo $language === 'Espanhol' ? 'selected' : ''; ?>>Espanhol</option>
                <option value="Francês" <?php echo $language === 'Francês' ? 'selected' : ''; ?>>Francês</option>
                <option value="Português" <?php echo $language === 'Português' ? 'selected' : ''; ?>>Português</option>
            </select>
        </div>
        
        <div class="filter-group" id="educationFilter">
            <label><i class="fas fa-graduation-cap"></i> Formação</label>
            <select name="education">
                <option value="">Qualquer</option>
                <option value="Ensino Superior" <?php echo $education === 'Ensino Superior' ? 'selected' : ''; ?>>Ensino Superior</option>
                <option value="Pós-graduação" <?php echo $education === 'Pós-graduação' ? 'selected' : ''; ?>>Pós-graduação</option>
                <option value="Mestrado" <?php echo $education === 'Mestrado' ? 'selected' : ''; ?>>Mestrado</option>
                <option value="Doutorado" <?php echo $education === 'Doutorado' ? 'selected' : ''; ?>>Doutorado</option>
            </select>
        </div>
        
        <button type="submit" class="btn-filtrar"><i class="fas fa-filter"></i> Filtrar</button>
    </form>
</div>

<div class="section-title" style="margin-top: 8px;">
    <i class="fas fa-list-ul"></i> Resultados (<?php echo $count; ?>)
</div>

<?php if (empty($results)): ?>
    <p style="text-align: center; padding: 40px; color: #555;">Nenhum resultado encontrado.</p>
<?php else: ?>
    <?php if ($search_type === 'candidates'): ?>
        <div class="card-grid">
            <?php foreach ($results as $p): ?>
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
    <?php else: ?>
        <?php foreach ($results as $j): ?>
        <div class="job-card">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap;">
                <div>
                    <h3><?php echo htmlspecialchars($j['title']); ?></h3>
                    <div class="company"><i class="fas fa-building"></i> <?php echo htmlspecialchars($j['company_name']); ?></div>
                </div>
                <span class="tag"><?php echo htmlspecialchars($j['job_type'] ?? 'CLT'); ?></span>
            </div>
            <div class="job-meta">
                <span><i class="fas fa-map-pin"></i> <?php echo htmlspecialchars($j['location'] ?? 'Não informado'); ?></span>
                <span><i class="fas fa-dollar-sign"></i> <?php echo htmlspecialchars($j['salary_range'] ?? 'A combinar'); ?></span>
                <span><i class="fas fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($j['created_at'])); ?></span>
            </div>
            <p style="margin: 8px 0; color: #444;"><?php echo substr(htmlspecialchars($j['description']), 0, 150) . '...'; ?></p>
            <a href="index.php?page=job_detail&id=<?php echo $j['id']; ?>" class="btn-link">Ver vaga <i class="fas fa-arrow-right"></i></a>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
<?php endif; ?>