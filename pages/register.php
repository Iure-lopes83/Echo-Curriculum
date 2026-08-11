<?php
// ============================================
// register.php - Cadastro simplificado
// ============================================

// Incluir config com caminho absoluto
require_once __DIR__ . '/../config.php';

// Ativar exibição de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);

$error = '';
$success = '';
$step = isset($_GET['step']) ? $_GET['step'] : 'choose';

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    
    // Pegar dados
    $user_type = $_POST['user_type'] ?? 'candidate';
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $photo = trim($_POST['photo'] ?? '');
    $description = trim($_POST['description'] ?? '');
    
    $errors = [];
    
    // Validações básicas
    if (empty($name)) $errors[] = 'Nome é obrigatório.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'E-mail inválido.';
    if (strlen($password) < 8) $errors[] = 'Senha deve ter pelo menos 8 caracteres.';
    
    // Verificar e-mail duplicado
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = 'Este e-mail já está cadastrado.';
            }
        } catch (Exception $e) {
            $errors[] = 'Erro ao verificar e-mail: ' . $e->getMessage();
        }
    }
    
    // Validações específicas
    if ($user_type === 'candidate') {
        $age = intval($_POST['age'] ?? 0);
        $cpf = trim($_POST['cpf'] ?? '');
        $education = trim($_POST['education'] ?? '');
        $experience = trim($_POST['experience'] ?? '');
        $languages = trim($_POST['languages'] ?? '');
        
        if ($age < 18) $errors[] = 'Idade deve ser maior ou igual a 18 anos.';
        if (empty($cpf)) $errors[] = 'CPF é obrigatório.';
        
        // Verificar CPF duplicado
        if (!empty($cpf)) {
            try {
                $stmt = $pdo->prepare("SELECT id FROM users WHERE cpf = ?");
                $stmt->execute([$cpf]);
                if ($stmt->fetch()) {
                    $errors[] = 'Este CPF já está cadastrado.';
                }
            } catch (Exception $e) {
                $errors[] = 'Erro ao verificar CPF: ' . $e->getMessage();
            }
        }
    } else if ($user_type === 'recruiter') {
        $company_name = trim($_POST['company_name'] ?? '');
        $cnpj = trim($_POST['cnpj'] ?? '');
        $company_description = trim($_POST['company_description'] ?? '');
        
        if (empty($company_name)) $errors[] = 'Nome da empresa é obrigatório.';
        if (empty($cnpj)) $errors[] = 'CNPJ é obrigatório.';
        
        // Verificar CNPJ duplicado
        if (!empty($cnpj)) {
            try {
                $stmt = $pdo->prepare("SELECT id FROM users WHERE cnpj = ?");
                $stmt->execute([$cnpj]);
                if ($stmt->fetch()) {
                    $errors[] = 'Este CNPJ já está cadastrado.';
                }
            } catch (Exception $e) {
                $errors[] = 'Erro ao verificar CNPJ: ' . $e->getMessage();
            }
        }
    }
    
    // Se não houver erros, cadastrar
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $pdo->beginTransaction();
            
            if ($user_type === 'candidate') {
                // Inserir candidato
                $stmt = $pdo->prepare("INSERT INTO users (name, age, email, cpf, password, phone, address, photo, description, user_type) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'candidate')");
                $stmt->execute([$name, $age, $email, $cpf, $hashed_password, $phone, $address, $photo, $description]);
                $user_id = $pdo->lastInsertId();
                
                // Inserir perfil
                $stmt = $pdo->prepare("INSERT INTO candidate_profiles (user_id, education, experience, languages) VALUES (?, ?, ?, ?)");
                $stmt->execute([$user_id, $education, $experience, $languages]);
                
            } else {
                // Inserir recrutador
                $stmt = $pdo->prepare("INSERT INTO users (name, email, cnpj, password, phone, address, photo, description, user_type) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'recruiter')");
                $stmt->execute([$name, $email, $cnpj, $hashed_password, $phone, $address, $photo, $description]);
                $user_id = $pdo->lastInsertId();
                
                // Inserir perfil da empresa
                $stmt = $pdo->prepare("INSERT INTO recruiter_profiles (user_id, company_name, company_description) VALUES (?, ?, ?)");
                $stmt->execute([$user_id, $company_name, $company_description]);
            }
            
            $pdo->commit();
            $success = true;
            $step = 'success';
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = 'Erro ao cadastrar: ' . $e->getMessage();
            $error = implode('<br>', $errors);
        }
    } else {
        $error = implode('<br>', $errors);
    }
}
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
                <div class="alert error">❌ <?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success === true): ?>
                <div class="alert success" style="text-align: center; padding: 30px 20px;">
                    <div style="font-size: 4rem; color: #28a745; margin-bottom: 16px;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h2 style="color: #155724; font-size: 1.8rem; margin-bottom: 8px;">✅ Cadastro Concluído!</h2>
                    <p style="color: #155724; font-size: 1.1rem; margin-bottom: 20px;">
                        Sua conta foi criada com sucesso!
                    </p>
                    <a href="index.php?page=login" class="btn-primary" style="display: inline-block; width: auto; padding: 14px 48px; text-decoration: none; font-size: 1.1rem;">
                        <i class="fas fa-sign-in-alt"></i> Ir para Login
                    </a>
                </div>
            <?php else: ?>
                <form method="POST">
                    <input type="hidden" name="user_type" value="candidate">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nome completo *</label>
                            <input type="text" name="name" placeholder="Ex: Ana Beatriz Souza" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Idade (≥18) *</label>
                            <input type="number" name="age" min="18" placeholder="25" value="<?php echo htmlspecialchars($_POST['age'] ?? ''); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>E-mail *</label>
                            <input type="email" name="email" placeholder="ana@email.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>CPF *</label>
                            <input type="text" name="cpf" placeholder="000.000.000-00" value="<?php echo htmlspecialchars($_POST['cpf'] ?? ''); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Senha (mín. 8 caracteres) *</label>
                            <input type="password" name="password" placeholder="••••••••" required>
                        </div>
                        <div class="form-group">
                            <label>Telefone</label>
                            <input type="text" name="phone" placeholder="(11) 98765-4321" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Formação acadêmica</label>
                        <input type="text" name="education" placeholder="Ex: Bacharel em Ciência da Computação" value="<?php echo htmlspecialchars($_POST['education'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Experiência profissional</label>
                        <textarea name="experience" placeholder="Descreva suas experiências..." rows="3"><?php echo htmlspecialchars($_POST['experience'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Idiomas</label>
                        <input type="text" name="languages" placeholder="Inglês (fluente), Espanhol (intermediário)" value="<?php echo htmlspecialchars($_POST['languages'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Endereço</label>
                        <input type="text" name="address" placeholder="Cidade, estado - país" value="<?php echo htmlspecialchars($_POST['address'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Foto (URL)</label>
                        <input type="text" name="photo" placeholder="https://exemplo.com/foto.jpg" value="<?php echo htmlspecialchars($_POST['photo'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Descrição pessoal</label>
                        <textarea name="description" placeholder="Sobre você, objetivos, habilidades..." rows="3"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <button type="submit" name="register" class="btn-primary">
                        <i class="fas fa-save"></i> Cadastrar Candidato
                    </button>
                </form>
                
                <div class="auth-switch">
                    Já tem conta? <a href="index.php?page=login">Faça login</a>
                </div>
            <?php endif; ?>
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
                <div class="alert error">❌ <?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success === true): ?>
                <div class="alert success" style="text-align: center; padding: 30px 20px;">
                    <div style="font-size: 4rem; color: #28a745; margin-bottom: 16px;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h2 style="color: #155724; font-size: 1.8rem; margin-bottom: 8px;">✅ Cadastro Concluído!</h2>
                    <p style="color: #155724; font-size: 1.1rem; margin-bottom: 20px;">
                        Sua empresa foi cadastrada com sucesso!
                    </p>
                    <a href="index.php?page=login" class="btn-primary" style="display: inline-block; width: auto; padding: 14px 48px; text-decoration: none; font-size: 1.1rem;">
                        <i class="fas fa-sign-in-alt"></i> Ir para Login
                    </a>
                </div>
            <?php else: ?>
                <form method="POST">
                    <input type="hidden" name="user_type" value="recruiter">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nome completo (responsável) *</label>
                            <input type="text" name="name" placeholder="Ex: Carlos Silva" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>E-mail *</label>
                            <input type="email" name="email" placeholder="contato@empresa.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nome da empresa *</label>
                            <input type="text" name="company_name" placeholder="Ex: TechNova Solutions" value="<?php echo htmlspecialchars($_POST['company_name'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>CNPJ *</label>
                            <input type="text" name="cnpj" placeholder="00.000.000/0000-00" value="<?php echo htmlspecialchars($_POST['cnpj'] ?? ''); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Senha (mín. 8 caracteres) *</label>
                            <input type="password" name="password" placeholder="••••••••" required>
                        </div>
                        <div class="form-group">
                            <label>Telefone</label>
                            <input type="text" name="phone" placeholder="(11) 98765-4321" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Descrição das atividades</label>
                        <textarea name="company_description" placeholder="Descreva o que sua empresa faz..." rows="3"><?php echo htmlspecialchars($_POST['company_description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Endereço</label>
                        <input type="text" name="address" placeholder="Cidade, estado - país" value="<?php echo htmlspecialchars($_POST['address'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Logo (URL)</label>
                        <input type="text" name="photo" placeholder="https://exemplo.com/logo.jpg" value="<?php echo htmlspecialchars($_POST['photo'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Descrição pessoal (do responsável)</label>
                        <textarea name="description" placeholder="Sobre você..." rows="2"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <button type="submit" name="register" class="btn-primary">
                        <i class="fas fa-save"></i> Cadastrar Empresa
                    </button>
                </form>
                
                <div class="auth-switch">
                    Já tem conta? <a href="index.php?page=login">Faça login</a>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.auth-container {
    max-width: 100%;
    margin: 20px auto;
    padding: 0 16px;
}

.auth-card .logo-ec {
    background: linear-gradient(135deg, #0a2647, #1c4e80);
    color: white;
    border-radius: 16px;
    letter-spacing: -2px;
}

.auth-card a:hover > div {
    border-color: var(--azul-medio);
    transform: translateY(-4px);
    box-shadow: 0 12px 28px rgba(10, 38, 71, 0.1);
}

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
    animation: fadeInUp 0.5s ease-out;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
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

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: var(--azul-medio);
    box-shadow: 0 0 0 4px rgba(28, 78, 128, 0.06);
    background: white;
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

.auth-switch {
    text-align: center;
    margin-top: 20px;
    color: #2d4e72;
}

.auth-switch a {
    color: var(--azul-medio);
    font-weight: 600;
    text-decoration: none;
}
</style>