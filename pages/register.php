<?php
// ============================================
// register.php - Cadastro (CORRIGIDO)
// ============================================
require_once __DIR__ . '/../config.php';

// Ativar exibição de erros para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

$error = '';
$success = false;
$step = isset($_GET['step']) ? $_GET['step'] : 'choose';

// Processar formulário ANTES de qualquer saída HTML
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
                $stmt = $pdo->prepare("INSERT INTO users (name, age, email, cpf, password, phone, address, photo, description, user_type) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'candidate')");
                $stmt->execute([$name, $age, $email, $cpf, $hashed_password, $phone, $address, $photo, $description]);
                $user_id = $pdo->lastInsertId();
                
                $stmt = $pdo->prepare("INSERT INTO candidate_profiles (user_id, education, experience, languages) VALUES (?, ?, ?, ?)");
                $stmt->execute([$user_id, $education, $experience, $languages]);
                
            } else {
                $stmt = $pdo->prepare("INSERT INTO users (name, email, cnpj, password, phone, address, photo, description, user_type) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'recruiter')");
                $stmt->execute([$name, $email, $cnpj, $hashed_password, $phone, $address, $photo, $description]);
                $user_id = $pdo->lastInsertId();
                
                $stmt = $pdo->prepare("INSERT INTO recruiter_profiles (user_id, company_name, company_description) VALUES (?, ?, ?)");
                $stmt->execute([$user_id, $company_name, $company_description]);
            }
            
            $pdo->commit();
            $success = true;
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = 'Erro ao cadastrar: ' . $e->getMessage();
            $error = implode('<br>', $errors);
        }
    } else {
        $error = implode('<br>', $errors);
    }
}

// Depois de todo o processamento, incluir o header
// OBS: Se $success for true, vamos redirecionar para login
if ($success === true) {
    // Limpar buffer e redirecionar
    while (ob_get_level()) {
        ob_end_clean();
    }
    header("Location: index.php?page=login?registered=1");
    exit();
}

// Se chegou aqui, continua com o HTML normalmente
?>
<!-- O HTML continua aqui... -->
