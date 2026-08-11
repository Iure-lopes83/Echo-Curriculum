<?php
// ============================================
// init.php - Inicialização do sistema
// ============================================

// Carregar configurações
require_once __DIR__ . '/config.php';

// Definir página atual
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$allowed_pages = [
    'home', 'login', 'register', 'search', 
    'candidate', 'recruiter', 'jobs', 'job_detail',
    'post_job', 'upload_resume', 'profile', 'logout'
];

if (!in_array($page, $allowed_pages)) {
    $page = 'home';
}

// ============================================
// PROCESSAR LOGOUT
// ============================================
if ($page === 'logout') {
    $_SESSION = array();
    session_destroy();
    redirect('index.php?page=home');
}

// ============================================
// PROCESSAR LOGIN
// ============================================
if ($page === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $login = trim($_POST['login_input'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($login) || empty($password)) {
        redirect('index.php?page=login&error=Preencha todos os campos');
    }
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE cpf = ? OR cnpj = ? OR email = ?");
        $stmt->execute([$login, $login, $login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_type'] = $user['user_type'];
            redirect('index.php?page=home');
        } else {
            redirect('index.php?page=login&error=Credenciais inválidas');
        }
    } catch (Exception $e) {
        redirect('index.php?page=login&error=Erro no sistema');
    }
}

// ============================================
// PROCESSAR CADASTRO (COMPLETO)
// ============================================
if ($page === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    
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
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'E-mail inválido.';
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
            $errors[] = 'Erro ao verificar e-mail.';
        }
    }
    
    // Validações para Candidato
    if ($user_type === 'candidate') {
        $age = intval($_POST['age'] ?? 0);
        $cpf = trim($_POST['cpf'] ?? '');
        $education = trim($_POST['education'] ?? '');
        $experience = trim($_POST['experience'] ?? '');
        $languages = trim($_POST['languages'] ?? '');
        
        if ($age < 18) $errors[] = 'Idade deve ser maior ou igual a 18 anos.';
        if (empty($cpf)) $errors[] = 'CPF é obrigatório.';
        
        if (!empty($cpf)) {
            try {
                $stmt = $pdo->prepare("SELECT id FROM users WHERE cpf = ?");
                $stmt->execute([$cpf]);
                if ($stmt->fetch()) {
                    $errors[] = 'Este CPF já está cadastrado.';
                }
            } catch (Exception $e) {
                $errors[] = 'Erro ao verificar CPF.';
            }
        }
    } 
    // Validações para Recrutador
    else if ($user_type === 'recruiter') {
        $company_name = trim($_POST['company_name'] ?? '');
        $cnpj = trim($_POST['cnpj'] ?? '');
        $company_description = trim($_POST['company_description'] ?? '');
        
        if (empty($company_name)) $errors[] = 'Nome da empresa é obrigatório.';
        if (empty($cnpj)) $errors[] = 'CNPJ é obrigatório.';
        
        if (!empty($cnpj)) {
            try {
                $stmt = $pdo->prepare("SELECT id FROM users WHERE cnpj = ?");
                $stmt->execute([$cnpj]);
                if ($stmt->fetch()) {
                    $errors[] = 'Este CNPJ já está cadastrado.';
                }
            } catch (Exception $e) {
                $errors[] = 'Erro ao verificar CNPJ.';
            }
        }
    }
    
    // Se houver erros, redirecionar de volta
    if (!empty($errors)) {
        $error_msg = implode('\\n', $errors);
        redirect("index.php?page=register&step=$user_type&error=" . urlencode($error_msg));
    }
    
    // ========== CADASTRAR ==========
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    try {
        $pdo->beginTransaction();
        
        if ($user_type === 'candidate') {
            // Inserir usuário
            $stmt = $pdo->prepare("INSERT INTO users (name, age, email, cpf, password, phone, address, photo, description, user_type) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'candidate')");
            $stmt->execute([$name, $age, $email, $cpf, $hashed_password, $phone, $address, $photo, $description]);
            $user_id = $pdo->lastInsertId();
            
            // Inserir perfil do candidato
            $stmt = $pdo->prepare("INSERT INTO candidate_profiles (user_id, education, experience, languages) VALUES (?, ?, ?, ?)");
            $stmt->execute([$user_id, $education, $experience, $languages]);
            
        } else {
            // Inserir usuário
            $stmt = $pdo->prepare("INSERT INTO users (name, email, cnpj, password, phone, address, photo, description, user_type) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'recruiter')");
            $stmt->execute([$name, $email, $cnpj, $hashed_password, $phone, $address, $photo, $description]);
            $user_id = $pdo->lastInsertId();
            
            // Inserir perfil da empresa
            $stmt = $pdo->prepare("INSERT INTO recruiter_profiles (user_id, company_name, company_description) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $company_name, $company_description]);
        }
        
        $pdo->commit();
        redirect('index.php?page=login&registered=1');
        
    } catch (Exception $e) {
        $pdo->rollBack();
        redirect("index.php?page=register&step=$user_type&error=" . urlencode('Erro ao cadastrar: ' . $e->getMessage()));
    }
}
?>
