<?php
// ============================================
// setup_db.php - Criar tabelas automaticamente
// ============================================
require_once __DIR__ . '/config.php';

echo "<h1>Configurando Banco de Dados</h1>";

$sqls = [
    // Tabela users
    "CREATE TABLE IF NOT EXISTS users (
        id SERIAL PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        age INT,
        email VARCHAR(100) UNIQUE NOT NULL,
        cpf VARCHAR(14) UNIQUE DEFAULT NULL,
        cnpj VARCHAR(18) UNIQUE DEFAULT NULL,
        password VARCHAR(255) NOT NULL,
        phone VARCHAR(20),
        address TEXT,
        photo VARCHAR(255),
        description TEXT,
        user_type VARCHAR(20) DEFAULT 'candidate',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    // Tabela candidate_profiles
    "CREATE TABLE IF NOT EXISTS candidate_profiles (
        id SERIAL PRIMARY KEY,
        user_id INT UNIQUE NOT NULL,
        education TEXT,
        experience TEXT,
        languages TEXT,
        resume_file VARCHAR(255),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )",
    
    // Tabela recruiter_profiles
    "CREATE TABLE IF NOT EXISTS recruiter_profiles (
        id SERIAL PRIMARY KEY,
        user_id INT UNIQUE NOT NULL,
        company_name VARCHAR(100) NOT NULL,
        company_description TEXT,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )",
    
    // Tabela jobs
    "CREATE TABLE IF NOT EXISTS jobs (
        id SERIAL PRIMARY KEY,
        recruiter_id INT NOT NULL,
        title VARCHAR(100) NOT NULL,
        description TEXT NOT NULL,
        requirements TEXT,
        location VARCHAR(100),
        salary_range VARCHAR(50),
        job_type VARCHAR(20) DEFAULT 'CLT',
        status VARCHAR(20) DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (recruiter_id) REFERENCES users(id) ON DELETE CASCADE
    )",
    
    // Tabela applications
    "CREATE TABLE IF NOT EXISTS applications (
        id SERIAL PRIMARY KEY,
        job_id INT NOT NULL,
        candidate_id INT NOT NULL,
        status VARCHAR(20) DEFAULT 'pending',
        applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
        FOREIGN KEY (candidate_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE (job_id, candidate_id)
    )"
];

$success = 0;
$errors = [];

foreach ($sqls as $sql) {
    try {
        $pdo->exec($sql);
        $success++;
        echo "<p style='color:green;'>✅ Tabela criada com sucesso</p>";
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
        echo "<p style='color:red;'>❌ Erro: " . $e->getMessage() . "</p>";
    }
}

echo "<h2>Resumo: $success tabelas criadas, " . count($errors) . " erros</h2>";

if (empty($errors)) {
    echo "<p style='color:green; font-size: 1.2rem;'>✅ Banco de dados configurado com sucesso!</p>";
    echo "<p><a href='index.php'>Voltar para o site</a></p>";
} else {
    echo "<p style='color:red;'>⚠️ Algumas tabelas não foram criadas. Verifique os erros acima.</p>";
}
?>
