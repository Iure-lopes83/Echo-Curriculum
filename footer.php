<?php
// ============================================
// footer.php - Rodapé (COM DROPDOWN)
// ============================================
// NÃO coloque nada antes desta linha!
?>
<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <span class="logo-ec" style="font-size: 1.5rem;">EC</span>
                <span style="font-weight: 600; color: var(--azul-profundo);">Echo Curriculum</span>
                <p style="margin-top: 8px; font-size: 0.85rem; color: #4a6a8a;">Conectando talentos e oportunidades</p>
            </div>
            <div class="footer-section">
                <h4>Links Rápidos</h4>
                <a href="index.php?page=home">Início</a>
                <a href="index.php?page=jobs">Vagas</a>
                <a href="index.php?page=search">Pesquisar</a>
            </div>
            <div class="footer-section">
                <h4>Para Empresas</h4>
                <a href="index.php?page=register">Cadastrar Empresa</a>
                <a href="index.php?page=post_job">Publicar Vaga</a>
            </div>
            <div class="footer-section">
                <h4>Contato</h4>
                <p><i class="fas fa-envelope"></i> contato@echocurriculum.com</p>
                <p><i class="fas fa-phone"></i> (11) 4000-0000</p>
            </div>
        </div>
        <div class="footer-bottom">
            <i class="fas fa-shield-alt" style="color: var(--azul-medio);"></i> 
            Banco de currículos seguro · Sistema ATS · &copy; 2026 EC - Echo Curriculum
        </div>
    </div>
</footer>

<!-- Script para controle de tema e dropdown -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ===== DROPDOWN =====
    const dropdownToggle = document.getElementById('settingsDropdown');
    const dropdownMenu = document.getElementById('settingsMenu');
    
    if (dropdownToggle && dropdownMenu) {
        // Abrir/fechar dropdown
        dropdownToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdownMenu.classList.toggle('open');
        });
        
        // Fechar dropdown ao clicar fora
        document.addEventListener('click', function(e) {
            if (!dropdownToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('open');
            }
        });
        
        // Fechar dropdown ao pressionar ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                dropdownMenu.classList.remove('open');
            }
        });
    }
    
    // ===== TEMA =====
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    const themeText = document.getElementById('themeText');
    
    // Verificar preferência salva
    const savedTheme = localStorage.getItem('ec_theme');
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-mode');
        themeIcon.className = 'fas fa-sun';
        themeText.textContent = 'Tema Claro';
    }
    
    // Toggle do tema
    if (themeToggle) {
        themeToggle.addEventListener('click', function(e) {
            e.stopPropagation(); // Evitar fechar o dropdown
            const isDark = document.body.classList.toggle('dark-mode');
            
            if (isDark) {
                themeIcon.className = 'fas fa-sun';
                themeText.textContent = 'Tema Claro';
                localStorage.setItem('ec_theme', 'dark');
            } else {
                themeIcon.className = 'fas fa-moon';
                themeText.textContent = 'Tema Escuro';
                localStorage.setItem('ec_theme', 'light');
            }
            
            // Fechar dropdown após selecionar
            if (dropdownMenu) {
                dropdownMenu.classList.remove('open');
            }
        });
    }
});
</script>
</body>
</html>
