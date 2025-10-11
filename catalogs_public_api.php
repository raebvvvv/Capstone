<?php
require __DIR__ . '/config.php';
require app_path('conn.php');
if (function_exists('secure_bootstrap')) { secure_bootstrap(); }

header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store, max-age=0');

function error_out($msg, $code = 400) {
    http_response_code($code);
    echo json_encode(['ok' => false, 'error' => (string)$msg], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// Ensure tables exist (same as admin API, minimal)
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS campuses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        code VARCHAR(50) NULL,
        UNIQUE KEY uq_campus_name (name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $pdo->exec("CREATE TABLE IF NOT EXISTS academic_levels (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        code VARCHAR(50) NULL,
        UNIQUE KEY uq_levels_name (name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $pdo->exec("CREATE TABLE IF NOT EXISTS colleges (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        code VARCHAR(50) NULL,
        campus_id INT NULL,
        UNIQUE KEY uq_college_name (name),
        KEY idx_campus_id (campus_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $pdo->exec("CREATE TABLE IF NOT EXISTS departments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        code VARCHAR(50) NULL,
        college_id INT NULL,
        UNIQUE KEY uq_department_name_college (name, college_id),
        KEY idx_college_id (college_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $pdo->exec("CREATE TABLE IF NOT EXISTS programs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        code VARCHAR(50) NULL,
        college_id INT NULL,
        UNIQUE KEY uq_program_name_college (name, college_id),
        KEY idx_college_id (college_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $pdo->exec("CREATE TABLE IF NOT EXISTS documents (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        code VARCHAR(50) NULL,
        role VARCHAR(20) NOT NULL DEFAULT 'both',
        UNIQUE KEY uq_document_name_role (name, role)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
} catch (Throwable $e) { error_out('Init error: '.$e->getMessage(), 500); }

$entity = isset($_GET['entity']) ? strtolower(trim($_GET['entity'])) : '';
$valid = ['campus','level','college','department','program','document'];
if (!in_array($entity, $valid, true)) { error_out('Unknown entity'); }

$parent_id = isset($_GET['parent_id']) ? (int)$_GET['parent_id'] : 0;
$role = isset($_GET['role']) ? strtolower(trim($_GET['role'])) : '';
if ($role !== '' && !in_array($role, ['student','employee','both'], true)) { $role = ''; }

try {
    switch ($entity) {
        case 'campus':
            $stmt = $pdo->query("SELECT id, name, code FROM campuses ORDER BY name");
            echo json_encode(['ok'=>true,'data'=>$stmt->fetchAll(PDO::FETCH_ASSOC)], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            break;
        case 'level':
            $stmt = $pdo->query("SELECT id, name, code FROM academic_levels ORDER BY name");
            echo json_encode(['ok'=>true,'data'=>$stmt->fetchAll(PDO::FETCH_ASSOC)], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            break;
        case 'college':
            if ($parent_id > 0) { $stmt = $pdo->prepare("SELECT id, name, code FROM colleges WHERE campus_id = ? ORDER BY name"); $stmt->execute([$parent_id]); }
            else { $stmt = $pdo->query("SELECT id, name, code FROM colleges ORDER BY name"); }
            echo json_encode(['ok'=>true,'data'=>$stmt->fetchAll(PDO::FETCH_ASSOC)], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            break;
        case 'department':
            if ($parent_id > 0) { $stmt = $pdo->prepare("SELECT id, name, code FROM departments WHERE college_id = ? ORDER BY name"); $stmt->execute([$parent_id]); }
            else { $stmt = $pdo->query("SELECT id, name, code FROM departments ORDER BY name"); }
            echo json_encode(['ok'=>true,'data'=>$stmt->fetchAll(PDO::FETCH_ASSOC)], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            break;
        case 'program':
            if ($parent_id > 0) { $stmt = $pdo->prepare("SELECT id, name, code FROM programs WHERE college_id = ? ORDER BY name"); $stmt->execute([$parent_id]); }
            else { $stmt = $pdo->query("SELECT id, name, code FROM programs ORDER BY name"); }
            echo json_encode(['ok'=>true,'data'=>$stmt->fetchAll(PDO::FETCH_ASSOC)], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            break;
        case 'document':
            if ($role === 'student' || $role === 'employee' || $role === 'both') {
                $stmt = $pdo->prepare("SELECT id, name, code, role FROM documents WHERE role IN ('both', ?) ORDER BY name");
                $stmt->execute([$role]);
            } else {
                $stmt = $pdo->query("SELECT id, name, code, role FROM documents ORDER BY name");
            }
            echo json_encode(['ok'=>true,'data'=>$stmt->fetchAll(PDO::FETCH_ASSOC)], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            break;
    }
} catch (Throwable $e) { error_out('Query error: '.$e->getMessage(), 500); }
