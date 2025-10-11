<?php
// Optional helper to seed catalogs from existing hardcoded arrays (idempotent best-effort)
require __DIR__ . '/../config.php';
require app_path('conn.php');
if (function_exists('secure_bootstrap')) { secure_bootstrap(); }
require_admin();

header('Content-Type: text/plain; charset=utf-8');

function upsert(PDO $pdo, $table, $name, $code = null, $parentKey = null, $parentId = null) {
    $name = trim($name); if ($name === '') return 0;
    $cols = ['name']; $vals = [$name]; $sets = ['name = VALUES(name)'];
    if ($code !== null) { $cols[] = 'code'; $vals[] = $code; $sets[] = 'code = VALUES(code)'; }
    if ($parentKey !== null) { $cols[] = $parentKey; $vals[] = $parentId; $sets[] = "$parentKey = VALUES($parentKey)"; }
    $sql = 'INSERT INTO `'.$table.'` ('.implode(',', $cols).') VALUES ('.str_repeat('?,', count($cols)-1).'?) ' .
           'ON DUPLICATE KEY UPDATE '.implode(',', $sets);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($vals);
    return (int)$pdo->lastInsertId();
}

function upsert_document(PDO $pdo, $name, $code = null, $role = 'both') {
  $name = trim($name); if ($name === '') return 0;
  $role = in_array($role, ['student','employee','both'], true) ? $role : 'both';
  $sql = 'INSERT INTO `documents` (name, code, role) VALUES (?,?,?) '
     . 'ON DUPLICATE KEY UPDATE code = VALUES(code)';
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$name, $code, $role]);
  return (int)$pdo->lastInsertId();
}

// Ensure base tables exist by calling API ensure
// Ensure tables exist
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

  // Documents table
  $pdo->exec("CREATE TABLE IF NOT EXISTS documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) NULL,
    role ENUM('student','employee','both') NOT NULL DEFAULT 'both',
    UNIQUE KEY uq_document_name_role (name, role)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
} catch (Throwable $e) { echo "Failed to ensure tables: ".$e->getMessage()."\n"; exit(1); }

echo "Seeding catalogs...\n";

// Current datasets (merged from student/employee dropdowns)
$campuses = [ 'PUP Main (Sta. Mesa, Manila)' => 'MAIN' ];

$levels = [
  'Undergraduate' => 'UG',
  'Masters' => 'MS',
  'Doctorate' => 'PhD',
  'Open University' => 'OU',
  'Not Studying' => 'NS',
];

$colleges = [
  'College of Accountancy and Finance (CAF)' => 'CAF',
  'College of Architecture, Design and the Built Environment (CADBE)' => 'CADBE',
  'College of Arts and Letters (CAL)' => 'CAL',
  'College of Business Administration (CBA)' => 'CBA',
  'College of Communication (COC)' => 'COC',
  'College of Computer and Information Sciences (CCIS)' => 'CCIS',
  'College of Education (COED)' => 'COED',
  'College of Engineering (CE)' => 'CE',
  'College of Human Kinetics (CHK)' => 'CHK',
  'College of Law (CL)' => 'CL',
  'College of Political Science and Public Administration (CPSPA)' => 'CPSPA',
  'College of Social Sciences and Development (CSSD)' => 'CSSD',
  'College of Science (CS)' => 'CS',
  'College of Tourism, Hospitality and Transportation Management (CTHTM)' => 'CTHTM',
  'Institute of Technology' => 'ITech',
];

$departmentsByCollege = [
  // From student file
  'College of Accountancy and Finance (CAF)' => [
    'Department of Accountancy',
    'Department of Finance and Economics',
    'Department of Management Accounting',
  ],
  'College of Computer and Information Sciences (CCIS)' => [
    'Department of Computer Science',
    'Department of Information Technology',
  ],
  'College of Engineering (CE)' => [
    'Department of Civil Engineering',
    'Department of Computer Engineering',
    'Department of Electrical Engineering',
    'Department of Electronics Engineering',
    'Department of Industrial Engineering',
    'Department of Mechanical Engineering',
    'Department of Railway Engineering',
  ],
  // From employee file
  'College of Business Administration (CBA)' => [
    'Department of Business Administration',
    'Department of Entrepreneurship',
    'Department of Office Administration',
  ],
];

$programsMap = [
  'College of Accountancy and Finance (CAF)' => [
    'Bachelor of Science in Accountancy (BSA)',
    'Bachelor of Science in Business Administration Major in Financial Management (BSBAFM)',
    'Bachelor of Science in Management Accounting (BSMA)'
  ],
  'College of Architecture, Design and the Built Environment (CADBE)' => [
    'Bachelor of Science in Architecture (BS-ARCH)',
    'Bachelor of Science in Interior Design (BSID)',
    'Bachelor of Science in Environmental Planning (BSEP)'
  ],
  'College of Arts and Letters (CAL)' => [
    'Bachelor of Arts in English Language Studies (ABELS)',
    'Bachelor of Arts in Filipinology (ABF)',
    'Bachelor of Arts in Literary and Cultural Studies (ABLCS)',
    'Bachelor of Arts in Philosophy (AB-PHILO)',
    'Bachelor of Performing Arts major in Theater Arts (BPEA)'
  ],
  'College of Business Administration (CBA)' => [
    'Doctor in Business Administration (DBA)',
    'Master in Business Administration (MBA)',
    'Bachelor of Science in Business Administration major in Human Resource Management (BSBAHRM)',
    'Bachelor of Science in Business Administration major in Marketing Management (BSBA-MM)',
    'Bachelor of Science in Entrepreneurship (BSENTREP)',
    'Bachelor of Science in Office Administration (BSOA)'
  ],
  'College of Communication (COC)' => [
    'Bachelor in Advertising and Public Relations (BADPR)',
    'Bachelor of Arts in Broadcasting (BA Broadcasting)',
    'Bachelor of Arts in Communication Research (BACR)',
    'Bachelor of Arts in Journalism (BAJ)'
  ],
  'College of Computer and Information Sciences (CCIS)' => [
    'Bachelor of Science in Computer Science (BSCS)',
    'Bachelor of Science in Information Technology (BSIT)'
  ],
  'College of Education (COED)' => [
    'Doctor of Philsophy in Education Management (PhDEM)',
    'Master of Arts in Education Management (MAEM)',
    'Master in Business Education (MBE)',
    'Master in Library and Information Science (MLIS)',
    'Master of Arts in English Language Teaching (MAELT)',
    'Master of Arts in Education major in Mathematics Education (MAEd-ME)',
    'Master of Arts in Physical Education and Sports (MAPES)',
    'Master of Arts in Education major in Teaching in the Challenged Areas (MAED-TCA)',
    'Post-Baccalaureate Diploma in Education (PBDE)',
    'Bachelor of Technology and Livelihood Education - Home Economics (BTLEd)',
    'Bachelor of Technology and Livelihood Education - Industrial Arts (BTLEd)',
    'Bachelor of Technology and Livelihood Education - ICT (BTLEd)',
    'Bachelor of Library and Information Science (BLIS)',
    'Bachelor of Secondary Education - English (BSEd)',
    'Bachelor of Secondary Education - Mathematics (BSEd)',
    'Bachelor of Secondary Education - Science (BSEd)',
    'Bachelor of Secondary Education - Filipino (BSEd)',
    'Bachelor of Secondary Education - Social Studies (BSEd)',
    'Bachelor of Elementary Education (BEEd)',
    'Bachelor of Early Childhood Education (BECEd)'
  ],
  'College of Engineering (CE)' => [
    'Bachelor of Science in Civil Engineering (BSCE)',
    'Bachelor of Science in Computer Engineering (BSCpE)',
    'Bachelor of Science in Electrical Engineering (BSEE)',
    'Bachelor of Science in Electronics Engineering (BSECE)',
    'Bachelor of Science in Industrial Engineering (BSIE)',
    'Bachelor of Science in Mechanical Engineering (BSME)',
    'Bachelor of Science in Railway Engineering (BSRE)'
  ],
  'College of Human Kinetics (CHK)' => [
    'Bachelor of Physical Education (BPE)',
    'Bachelor of Science in Exercises and Sports (BSESS)'
  ],
  'College of Law (CL)' => [ 'Juris Doctor (JD)' ],
  'College of Political Science and Public Administration (CPSPA)' => [
    'Doctor in Public Administration (DPA)',
    'Master in Public Administration (MPA)',
    'Bachelor of Arts in Political Science (BAPS)',
    'Bachelor of Arts in Political Economy (BAPE)',
    'Bachelor of Arts in International Studies (BAIS)',
    'Bachelor of Public Administration (BPA)'
  ],
  'College of Social Sciences and Development (CSSD)' => [
    'Bachelor of Arts in History (BAH)',
    'Bachelor of Arts in Sociology (BAS)',
    'Bachelor of Science in Cooperatives (BSC)',
    'Bachelor of Science in Economics (BSE)',
    'Bachelor of Science in Psychology (BSPSY)'
  ],
  'College of Science (CS)' => [
    'Bachelor of Science Food Technology (BSFT)',
    'Bachelor of Science in Applied Mathematics (BSAPMATH)',
    'Bachelor of Science in Biology (BSBIO)',
    'Bachelor of Science in Chemistry (BSCHEM)',
    'Bachelor of Science in Mathematics (BSMATH)',
    'Bachelor of Science in Nutrition and Dietetics (BSND)',
    'Bachelor of Science in Physics (BSPHY)',
    'Bachelor of Science in Statistics (BSSTAT)'
  ],
  'College of Tourism, Hospitality and Transportation Management (CTHTM)' => [
    'Bachelor of Science in Hospitality Management (BSHM)',
    'Bachelor of Science in Tourism Management (BSTM)',
    'Bachelor of Science in Transportation Management (BSTRM)'
  ],
  'Institute of Technology' => [
    'Diploma in Computer Engineering Technology (DCET)',
    'Diploma in Electrical Engineering Technology (DEET)',
    'Diploma in Electronics Engineering Technology (DECET)',
    'Diploma in Information Communication Technology (DICT)',
    'Diploma in Mechanical Engineering Technology (DMET)',
    'Diploma in Office Management (DOMT)'
  ],
  // Level-specific groupings (will have NULL college_id)
  'Masters' => [
    'Master in Applied Statistics (MAS)',
    'Master in Business Administration (MBA)',
    'Master in Construction Management (MCM)',
    'Master in Educational Management (MEM)',
    'Master in Public Administration (MPA)',
    'Master of Arts in Communication (MAC)',
    'Master of Arts in English Language Studies (MAELS)',
    'Master of Arts in History (MAH)',
    'Master of Arts in Filipino (MAF)',
    'Master of Arts in Psychology (MAP)',
    'Master of Arts in Technology Management (MATM)',
    'Master of Science in Biology (MSBio)',
    'Master of Science in Civil Engineering (MSCE)',
    'Master of Science in Computer Engineering (MSCpE)',
    'Master of Science in Computer Science (MSCS)',
    'Master of Science in Construction Management (MSCM)',
    'Master of Science in Information Technology (MSIT)',
    'Master of Science in Mathematics (MSM)'
  ],
  'Doctorate' => [
    'Doctor of Philosophy in Communication (PhD Com)',
    'Doctor of Philosophy in Economics (PhD Econ)',
    'Doctor of Philosophy in English Language Studies (PhD ELS)',
    'Doctor of Philosophy in Filipino (PhD Fil)',
    'Doctor of Philosophy in Psychology (PhD Psy)'
  ],
  'Open University' => [
    'Doctor in Business Administration (DBA)',
    'Doctor in Engineering Management (D.Eng)',
    'Doctor of Philsophy in Education Management (PhDEM)',
    'Doctor in Public Administration (DPA)',
    'Master in Communication (MC)',
    'Master in Business Administration (MBA)',
    'Master of Arts in Education Management (MAEM)',
    'Master in Information Technology (MIT)',
    'Master in Public Administration (MPA)',
    'Master of Science in Construction Management (MSCM)',
    'Post Baccalaureate Diploma in Information Technology (PBDIT)',
    'Bachelor of Science in Entrepreneurship (BSENTREP)',
    'Bachelor of Arts in Broadcasting (BABR)',
    'Bachelor of Science in Business Administration major in Human Resource Management (BSBAHRM)',
    'Bachelor of Science in Business Administration major in Marketing Management (BSBAMM)',
    'Bachelor of Science in Office Administration (BSOA)',
    'Bachelor of Science in Tourism Management (BSTM)',
    'Bachelor of Public Administration (BPA)',
    'Bachelor of Science in Business Administration (BSBA)',
    'Bachelor of Science in Information Technology (BSIT)'
  ],
];

// Seed documents based on current forms
// Student-required documents
$studentDocuments = [
  ['Journal Publication Format', 'JPF'],
  ['Notarized Copyright Application Form', 'NCAF'],
  ['Receipt of Payment', 'RCPT'],
  ['Full Manuscript', 'FMSS'],
  ['Notarized Co-Authorship', 'NCAU'],
  ['Approval Sheet (Thesis)', 'APRV'],
  ['Record of Copyright Application', 'ROCA'],
];

// Employee-required documents
$employeeDocuments = [
  ['Journal Publication Format', 'JPF'],
  ['Notarized Copyright Application Form', 'NCAF'],
  ['Receipt of Payment', 'RCPT'],
  ['Presentation', 'PRSN'],
  ['Record of Copyright Application', 'ROCA'],
  // Optional in employee form: Notarized Co-Authorship
  ['Notarized Co-Authorship', 'NCAU'],
];

// Seed campuses
$campusIds = [];
foreach ($campuses as $name => $code) {
    $id = upsert($pdo, 'campuses', $name, $code);
    if (!$id) { $stmt = $pdo->prepare('SELECT id FROM campuses WHERE name = ? LIMIT 1'); $stmt->execute([$name]); $id = (int)$stmt->fetchColumn(); }
    $campusIds[$name] = $id;
}

// Seed academic levels
foreach ($levels as $lv => $c) { upsert($pdo, 'academic_levels', $lv, $c); }

// Seed colleges (assign to PUP Main if available)
$campusId = $campusIds['PUP Main (Sta. Mesa, Manila)'] ?? null;
$collegeIds = [];
foreach ($colleges as $name => $code) {
    $id = upsert($pdo, 'colleges', $name, $code, 'campus_id', $campusId);
    if (!$id) { $stmt = $pdo->prepare('SELECT id FROM colleges WHERE name = ? LIMIT 1'); $stmt->execute([$name]); $id = (int)$stmt->fetchColumn(); }
    $collegeIds[$name] = $id;
}

// Seed departments
foreach ($departmentsByCollege as $collegeName => $deps) {
    $cid = $collegeIds[$collegeName] ?? null;
    if (!$cid) continue;
    foreach ($deps as $dname) { upsert($pdo, 'departments', $dname, null, 'college_id', $cid); }
}

// Helper to extract code from parentheses at the end, else null
function extract_code($name) {
    if (preg_match('/\(([^)]+)\)\s*$/', $name, $m)) return trim($m[1]);
    return null;
}

// Seed programs (college-specific and level-specific)
foreach ($programsMap as $key => $list) {
    $cid = $collegeIds[$key] ?? null; // will be null for Masters/Doctorate/Open University
    foreach ($list as $pname) {
        $pcode = extract_code($pname);
        upsert($pdo, 'programs', $pname, $pcode, 'college_id', $cid);
    }
}

// Seed documents: treat items present in both sets as 'both', otherwise as role-specific
$studentNames = array_column($studentDocuments, 0);
$employeeNames = array_column($employeeDocuments, 0);

$allNames = array_unique(array_merge($studentNames, $employeeNames));
foreach ($allNames as $docName) {
  // find code from either list
  $code = null;
  foreach ([$studentDocuments, $employeeDocuments] as $list) {
    foreach ($list as $row) { if ($row[0] === $docName) { $code = $row[1]; break 2; } }
  }
  $inStudent = in_array($docName, $studentNames, true);
  $inEmployee = in_array($docName, $employeeNames, true);
  $role = ($inStudent && $inEmployee) ? 'both' : ($inStudent ? 'student' : 'employee');
  upsert_document($pdo, $docName, $code, $role);
}

echo "Seeded documents.\n";

echo "Done.\n";
