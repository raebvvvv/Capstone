<?php
require __DIR__ . '/../config.php';
require app_path('conn.php');
if (function_exists('secure_bootstrap')) { secure_bootstrap(); }
require_admin();

header('Content-Type: application/json');

function fail($msg, $code = 400) {
    http_response_code($code);
    echo json_encode(['ok' => false, 'error' => $msg]);
    exit;
}

// CSRF check for mutating requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    $csrfHeader = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    $csrfField = $_POST['csrf_token'] ?? '';
    $sessionToken = $_SESSION['csrf_token'] ?? '';
    if (!$sessionToken || ($csrfHeader !== $sessionToken && $csrfField !== $sessionToken)) {
        fail('Invalid CSRF token', 403);
    }
}

// Ensure tables exist; minimal schema to support CRUD
function ensure_tables(PDO $pdo) {
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
        KEY idx_campus_id (campus_id),
        CONSTRAINT fk_college_campus FOREIGN KEY (campus_id) REFERENCES campuses(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS departments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        code VARCHAR(50) NULL,
        college_id INT NULL,
        UNIQUE KEY uq_department_name_college (name, college_id),
        KEY idx_college_id (college_id),
        CONSTRAINT fk_dept_college FOREIGN KEY (college_id) REFERENCES colleges(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS programs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        code VARCHAR(50) NULL,
        college_id INT NULL,
        UNIQUE KEY uq_program_name_college (name, college_id),
        KEY idx_college_id (college_id),
        CONSTRAINT fk_prog_college FOREIGN KEY (college_id) REFERENCES colleges(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS documents (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        code VARCHAR(50) NULL,
        role VARCHAR(20) NOT NULL DEFAULT 'both',
        UNIQUE KEY uq_document_name_role (name, role)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
}

try { ensure_tables($pdo); } catch (Throwable $e) { fail('Failed to ensure tables: ' . $e->getMessage(), 500); }

$action = $_GET['action'] ?? $_POST['action'] ?? 'list';
$entity = $_GET['entity'] ?? $_POST['entity'] ?? '';
$entity = strtolower(trim($entity));
$validEntities = ['campus','level','college','department','program','document'];
if (!in_array($entity, $validEntities, true)) { fail('Unknown entity'); }

// Helper to map entity -> table and parent foreign keys
function resolve_meta(string $entity): array {
    switch ($entity) {
        case 'campus': return ['table' => 'campuses', 'parent' => null, 'parentTable' => null, 'parentField' => null];
        case 'level': return ['table' => 'academic_levels', 'parent' => null, 'parentTable' => null, 'parentField' => null];
        case 'college': return ['table' => 'colleges', 'parent' => 'campus_id', 'parentTable' => 'campuses', 'parentField' => 'id'];
        case 'department': return ['table' => 'departments', 'parent' => 'college_id', 'parentTable' => 'colleges', 'parentField' => 'id'];
        case 'program': return ['table' => 'programs', 'parent' => 'college_id', 'parentTable' => 'colleges', 'parentField' => 'id'];
        case 'document': return ['table' => 'documents', 'parent' => null, 'parentTable' => null, 'parentField' => null];
    }
    return [];
}

$meta = resolve_meta($entity);
$table = $meta['table'];
$parentKey = $meta['parent'];
$parentTable = $meta['parentTable'];

try {
    if ($action === 'list') {
        // Optional parent filter (?parent_id=)
        $parentId = isset($_GET['parent_id']) ? (int)$_GET['parent_id'] : 0;
        if ($parentKey && $parentId > 0) {
            $stmt = $pdo->prepare("SELECT * FROM `$table` WHERE `$parentKey` = ? ORDER BY name");
            $stmt->execute([$parentId]);
        } else {
            $stmt = $pdo->query("SELECT * FROM `$table` ORDER BY name");
        }
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['ok' => true, 'data' => $rows]);
        exit;
    }

    if ($action === 'parents') {
        // Return parent options for an entity
        if (!$parentTable) { echo json_encode(['ok'=>true,'data'=>[]]); exit; }
        $stmt = $pdo->query("SELECT id, name, code FROM `$parentTable` ORDER BY name");
        echo json_encode(['ok'=>true,'data'=>$stmt->fetchAll(PDO::FETCH_ASSOC)]);
        exit;
    }

    // Mutations: create/update/delete
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = trim($_POST['name'] ?? '');
    $code = trim($_POST['code'] ?? '');
    $parentId = isset($_POST['parent_id']) && $_POST['parent_id'] !== '' ? (int)$_POST['parent_id'] : null;
    $role = isset($_POST['role']) ? trim($_POST['role']) : '';

    if ($action === 'create') {
        if ($name === '') fail('Name is required');
        if ($parentKey && $parentId === null) fail('Parent is required');
        $cols = ['name']; $vals = [$name]; $ph = ['?'];
        if ($code !== '') { $cols[] = 'code'; $vals[] = $code; $ph[] = '?'; }
        if ($parentKey) { $cols[] = $parentKey; $vals[] = $parentId; $ph[] = '?'; }
        if ($table === 'documents') { $cols[] = 'role'; $vals[] = ($role !== '' ? $role : 'both'); $ph[] = '?'; }
        $sql = 'INSERT INTO `'.$table.'` ('.implode(',', $cols).') VALUES ('.implode(',', $ph).')';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($vals);
        echo json_encode(['ok'=>true, 'id'=>(int)$pdo->lastInsertId()]);
        exit;
    }

    if ($action === 'update') {
        if ($id <= 0) fail('Invalid id');
        if ($name === '') fail('Name is required');
        $sets = ['name = ?']; $vals = [$name];
    if ($code !== '' || $code === '') { $sets[] = 'code = ?'; $vals[] = ($code !== '' ? $code : null); }
        if ($parentKey) { $sets[] = "$parentKey = ?"; $vals[] = ($parentId !== null ? $parentId : null); }
    if ($table === 'documents') { $sets[] = 'role = ?'; $vals[] = ($role !== '' ? $role : 'both'); }
        $vals[] = $id;
        $sql = 'UPDATE `'.$table.'` SET '.implode(',', $sets).' WHERE id = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($vals);
        echo json_encode(['ok'=>true]);
        exit;
    }

    if ($action === 'delete') {
        if ($id <= 0) fail('Invalid id');
        $stmt = $pdo->prepare('DELETE FROM `'.$table.'` WHERE id = ?');
        $stmt->execute([$id]);
        echo json_encode(['ok'=>true]);
        exit;
    }

    fail('Unknown action');
} catch (Throwable $e) {
    fail('Server error: '.$e->getMessage(), 500);
}
