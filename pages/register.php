<?php
// ============================================
// register.php - Formulário de cadastro
// ============================================
$step = isset($_GET['step']) ? $_GET['step'] : 'choose';
$error = isset($_GET['error']) ? $_GET['error'] : '';
?>
<div class="auth-container">
    <?php if ($step === 'choose' || empty($step)): ?>
        <!-- Tela de escolha -->
        <div class="auth-card" style="max-width: 700px;">
            <div style="text-align: center; margin-bottom: 30px;">
                <div class="logo-ec" style="display: inline-block; font-size: 3rem; padding: 10px 30px; margin-bottom: 10px;">EC</div>
                <h2 style="color: var(--azul-profundo); font-size: 2rem;">Crie sua conta</h2>
                <p class="subtitle" style="color: #4a6a8a;">Escolha como deseja se cadastrar</p>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                <a href="index.php?page=register&step=candidate" style="text-decoration: none;">
                    <div style="background: var(--branco); border-radius: 24px; padding: 30px 20px; text-align: center; border: 2px solid #e8eff6; transition: all 0.3s; cursor: pointer;">
                        <div style="width: 80px; height: 80px; border-radius: 50%; background: var(--azul-claro); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                            <i class="fas fa-user-graduate" style="font-size: 2.5rem; color: var(--azul-medio);"></i>
                        </div>
                        <h3 style="color: var(--azul-profundo);">Sou Candidato</h3>
                        <p style="color: #4a6a8a; font-size: 0.9rem;">Quero encontrar oportunidades</p>
                        <div style="margin-top: 16px; padding: 8px 24px; background: var(--azul-profundo); color: white; border-radius: 40px; font-weight: 600; font-size: 0.9rem; display: inline-block;">
                            Cadastrar
                        </div>
                    </div>
                </a>
                
                <a href="index.php?page=register&step=recruiter" style="text-decoration: none;">
                    <div style="background: var(--branco); border-radius: 24px; padding: 30px 20px; text-align: center; border: 2px solid #e8eff6; transition: all 0.3s; cursor: pointer;">
                        <div style="width: 80px; height: 80px; border-radius: 50%; background: var(--azul-claro); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                            <i class="fas fa-building" style="font-size: 2.5rem; color: var(--azul-medio);"></i>
                        </div>
                        <h3 style="color: var(--azul-profundo);">Sou Empresa</h3>
                        <p style="color: #4a6a8a; font-size: 0.9rem;">Quero encontrar talentos</p>
                        <div style="margin-top: 16px; padding: 8px 24px; background: var(--azul-profundo); color: white; border-radius: 40px; font-weight: 600; font-size: 0.9rem; display: inline-block;">
                            Cadastrar
                        </div>
                    </div>
                </a>
            </div>
            
            <div class="auth-switch" style="margin-top: 30px;">
                Já tem conta? <a href="index.php?page=login">Faça login</a>
            </div>
        </div>
        
    <?php elseif ($step === 'candidate'): ?>
        <!-- Formulário Candidato -->
        <div class="auth-card" style="max-width: 680px;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                <a href="index.php?page=register" style="color: var(--azul-medio); font-size: 1.2rem;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h2 style="color: var(--azul-profundo); font-size: 1.6rem;"><i class="fas fa-user-graduate"></i> Cadastro Candidato</h2>
                    <p class="subtitle" style="color: #4a6a8a;">Preencha seus dados</p>
                </div>
            </div>
            
            <?php if ($error): ?>
                <div class="alert error">
                    <strong>❌ Erro no cadastro:</strong><br>
                    <?php echo nl2br(htmlspecialchars(str_replace('\\n', "\n", $error))); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="index.php?page=register">
                <input type="hidden" name="user_type" value="candidate">
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Nome completo *</label>
                        <input type="text" name="name" placeholder="Ex: Ana Beatriz Souza" required>
                    </div>
                    <div class="form-group">
                        <label>Idade (≥18) *</label>
                        <input type="number" name="age" min="18" placeholder="25" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>E-mail *</label>
                        <input type="email" name="email" placeholder="ana@email.com" required>
                    </div>
                    <div class="form-group">
                        <label>CPF *</label>
                        <input type="text" name="cpf" placeholder="000.000.000-00" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Senha (mín. 8 caracteres) *</label>
                        <input type="password" name="password" placeholder="••••••••" required>
                    </div>
                    <div class="form-group">
                        <label>Telefone</label>
                        <input type="text" name="phone" placeholder="(11) 98765-4321">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Formação acadêmica</label>
                    <input type="text" name="education" placeholder="Ex: Bacharel em Ciência da Computação">
                </div>
                
                <div class="form-group">
                    <label>Experiência profissional</label>
                    <textarea name="experience" placeholder="Descreva suas experiências..." rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Idiomas</label>
                    <input type="text" name="languages" placeholder="Inglês (fluente), Espanhol (intermediário)">
                </div>
                
                <div class="form-group">
                    <label>Endereço</label>
                    <input type="text" name="address" placeholder="Cidade, estado - país">
                </div>
                
                <div class="form-group">
                    <label>Foto (URL)</label>
                    <input type="text" name="photo" placeholder="https://exemplo.com/foto.jpg">
                </div>
                
                <div class="form-group">
                    <label>Descrição pessoal</label>
                    <textarea name="description" placeholder="Sobre você, objetivos, habilidades..." rows="3"></textarea>
                </div>
                
                <button type="submit" name="register" class="btn-primary">
                    <i class="fas fa-save"></i> Cadastrar Candidato
                </button>
            </form>
            
            <div class="auth-switch">
                Já tem conta? <a href="index.php?page=login">Faça login</a>
            </div>
        </div>
        
    <?php elseif ($step === 'recruiter'): ?>
        <!-- Formulário Empresa -->
        <div class="auth-card" style="max-width: 680px;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                <a href="index.php?page=register" style="color: var(--azul-medio); font-size: 1.2rem;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h2 style="color: var(--azul-profundo); font-size: 1.6rem;"><i class="fas fa-building"></i> Cadastro Empresa</h2>
                    <p class="subtitle" style="color: #4a6a8a;">Cadastre sua empresa</p>
                </div>
            </div>
            
            <?php if ($error): ?>
                <div class="alert error">
                    <strong>❌ Erro no cadastro:</strong><br>
                    <?php echo nl2br(htmlspecialchars(str_replace('\\n', "\n", $error))); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="index.php?page=register">
                <input type="hidden" name="user_type" value="recruiter">
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Nome completo (responsável) *</label>
                        <input type="text" name="name" placeholder="Ex: Carlos Silva" required>
                    </div>
                    <div class="form-group">
                        <label>E-mail *</label>
                        <input type="email" name="email" placeholder="contato@empresa.com" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Nome da empresa *</label>
                        <input type="text" name="company_name" placeholder="Ex: TechNova Solutions" required>
                    </div>
                    <div class="form-group">
                        <label>CNPJ *</label>
                        <input type="text" name="cnpj" placeholder="00.000.000/0000-00" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Senha (mín. 8 caracteres) *</label>
                        <input type="password" name="password" placeholder="••••••••" required>
                    </div>
                    <div class="form-group">
                        <label>Telefone</label>
                        <input type="text" name="phone" placeholder="(11) 98765-4321">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Descrição das atividades</label>
                    <textarea name="company_description" placeholder="Descreva o que sua empresa faz..." rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Endereço</label>
                    <input type="text" name="address" placeholder="Cidade, estado - país">
                </div>
                
                <div class="form-group">
                    <label>Logo (URL)</label>
                    <input type="text" name="photo" placeholder="https://exemplo.com/logo.jpg">
                </div>
                
                <div class="form-group">
                    <label>Descrição pessoal (do responsável)</label>
                    <textarea name="description" placeholder="Sobre você..." rows="2"></textarea>
                </div>
                
                <button type="submit" name="register" class="btn-primary">
                    <i class="fas fa-save"></i> Cadastrar Empresa
                </button>
            </form>
            
            <div class="auth-switch">
                Já tem conta? <a href="index.php?page=login">Faça login</a>
            </div>
        </div>
    <?php endif; ?>
</div>
