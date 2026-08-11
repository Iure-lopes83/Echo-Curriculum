<?php
// ============================================
// menu.php - Menu de navegação (CORRIGIDO)
// ============================================
// NÃO coloque nada antes desta linha!
?>
<nav class="navbar">
    <div class="container">
        <div class="logo">
            <a href="index.php?page=home" class="logo-link">
                <span class="logo-ec">EC</span>
                <span class="logo-text">Echo Curriculum</span>
            </a>
        </div>
        <ul class="nav-links">
            <li><a href="index.php?page=home" class="<?php echo ($page === 'home') ? 'active' : ''; ?>"><i class="fas fa-home"></i> Início</a></li>
            <li><a href="index.php?page=jobs" class="<?php echo ($page === 'jobs') ? 'active' : ''; ?>"><i class="fas fa-briefcase"></i> Vagas</a></li>
            <li><a href="index.php?page=search" class="<?php echo ($page === 'search') ? 'active' : ''; ?>"><i class="fas fa-search"></i> Pesquisar</a></li>
            
            <?php if (isLoggedIn()): ?>
                <?php if (isCandidate()): ?>
                    <li><a href="index.php?page=upload_resume" class="<?php echo ($page === 'upload_resume') ? 'active' : ''; ?>"><i class="fas fa-upload"></i> Enviar Currículo</a></li>
                    <li><a href="index.php?page=candidate&id=<?php echo $_SESSION['user_id']; ?>" class="<?php echo ($page === 'candidate') ? 'active' : ''; ?>"><i class="fas fa-user"></i> Meu Perfil</a></li>
                <?php elseif (isRecruiter()): ?>
                    <li><a href="index.php?page=post_job" class="<?php echo ($page === 'post_job') ? 'active' : ''; ?>"><i class="fas fa-plus-circle"></i> Nova Vaga</a></li>
                    <li><a href="index.php?page=recruiter" class="<?php echo ($page === 'recruiter') ? 'active' : ''; ?>"><i class="fas fa-building"></i> Minha Empresa</a></li>
                <?php endif; ?>
                <li><a href="index.php?page=profile" class="<?php echo ($page === 'profile') ? 'active' : ''; ?>"><i class="fas fa-cog"></i> Configurar</a></li>
                <li><a href="index.php?page=logout" class="btn-outline"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
            <?php else: ?>
                <li><a href="index.php?page=login" class="btn-outline <?php echo ($page === 'login') ? 'active' : ''; ?>"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                <li><a href="index.php?page=register" class="btn-solid <?php echo ($page === 'register') ? 'active' : ''; ?>"><i class="fas fa-user-plus"></i> Cadastrar</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
