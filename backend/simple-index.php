<?php
// Simple PHP backend without Composer dependencies
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Database configuration
$host = 'localhost';
$dbname = 'marking_system';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Get request method and path
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove /api prefix if present
$path = str_replace('/api', '', $path);

// Debug: Log the request
error_log("Request: $method $path");

// Get request body for POST requests
$input = json_decode(file_get_contents('php://input'), true);

// Route handling
switch ($method) {
    case 'POST':
        switch ($path) {
            case '/login':
                handleLogin($pdo, $input);
                break;
            case '/logout':
                handleLogout();
                break;
            case '/add-student':
                handleAddStudent($pdo, $input);
                break;
            case '/add-marks':
                handleAddMarks($pdo, $input);
                break;
            default:
                http_response_code(404);
                echo json_encode(['error' => 'Endpoint not found', 'path' => $path, 'method' => $method, 'debug' => 'POST route not matched']);
                break;
        }
        break;

    case 'GET':
        switch ($path) {
            case '/check-auth':
                handleCheckAuth();
                break;
            case '/get-marks':
                handleGetMarks($pdo);
                break;
            case '/get-students':
                handleGetStudents($pdo);
                break;
            case '/get-students-without-marks':
                handleGetStudentsWithoutMarks($pdo);
                break;
            default:
                http_response_code(404);
                echo json_encode(['error' => 'Endpoint not found']);
                break;
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}

// Handler functions
function handleLogin($pdo, $input)
{
    $username = $input['username'] ?? '';
    $password = $input['password'] ?? '';

    if (empty($username) || empty($password)) {
        http_response_code(400);
        echo json_encode(['error' => 'Username and password are required']);
        return;
    }

    $stmt = $pdo->prepare("SELECT id, username, password, role, name FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];

        echo json_encode([
            'success' => true,
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role'],
                'name' => $user['name']
            ]
        ]);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid credentials']);
    }
}

function handleLogout()
{
    session_start();
    session_destroy();
    echo json_encode(['success' => true]);
}

function handleCheckAuth()
{
    session_start();

    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Not authenticated']);
        return;
    }

    echo json_encode([
        'user' => [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'role' => $_SESSION['role'],
            'name' => $_SESSION['name']
        ]
    ]);
}

function handleAddStudent($pdo, $input)
{
    session_start();

    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'lecturer') {
        http_response_code(403);
        echo json_encode(['error' => 'Unauthorized']);
        return;
    }

    $name = $input['name'] ?? '';
    $matricNumber = $input['matric_number'] ?? '';

    if (empty($name) || empty($matricNumber)) {
        http_response_code(400);
        echo json_encode(['error' => 'Student name and matric number are required']);
        return;
    }

    // Validate student name
    $name = trim($name);
    if (strlen($name) < 2) {
        http_response_code(400);
        echo json_encode(['error' => 'Student name must be at least 2 characters long']);
        return;
    }

    if (strlen($name) > 100) {
        http_response_code(400);
        echo json_encode(['error' => 'Student name must be less than 100 characters']);
        return;
    }

    // Validate name format (only letters, spaces, hyphens, and apostrophes)
    if (!preg_match('/^[a-zA-Z\s\'-]+$/', $name)) {
        http_response_code(400);
        echo json_encode(['error' => 'Student name can only contain letters, spaces, hyphens, and apostrophes']);
        return;
    }

    // Validate matric number
    $matricNumber = trim($matricNumber);
    if (strlen($matricNumber) < 3) {
        http_response_code(400);
        echo json_encode(['error' => 'Matric number must be at least 3 characters long']);
        return;
    }

    if (strlen($matricNumber) > 20) {
        http_response_code(400);
        echo json_encode(['error' => 'Matric number must be less than 20 characters']);
        return;
    }

    // Validate matric number format (alphanumeric and common symbols)
    if (!preg_match('/^[a-zA-Z0-9\-_\.]+$/', $matricNumber)) {
        http_response_code(400);
        echo json_encode(['error' => 'Matric number can only contain letters, numbers, hyphens, underscores, and dots']);
        return;
    }



    // Check if matric number already exists
    $checkStmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $checkStmt->execute([$matricNumber]);
    if ($checkStmt->fetch()) {
        http_response_code(400);
        echo json_encode(['error' => 'Student with this matric number already exists']);
        return;
    }

    // Hash the password (matric number as password)
    $hashedPassword = password_hash($matricNumber, PASSWORD_DEFAULT);

    // Insert new student into users table
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role, name) VALUES (?, ?, 'student', ?)");

    if ($stmt->execute([$matricNumber, $hashedPassword, $name])) {
        echo json_encode([
            'success' => true,
            'student_id' => $pdo->lastInsertId(),
            'message' => "Student added successfully. Username: $matricNumber, Password: $matricNumber"
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to add student']);
    }
}

function handleAddMarks($pdo, $input)
{
    session_start();

    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'lecturer') {
        http_response_code(403);
        echo json_encode(['error' => 'Unauthorized']);
        return;
    }

    $student_id = $input['student_id'] ?? '';
    $assignment = $input['assignment'] ?? '';
    $exam = $input['exam'] ?? '';

    if (empty($student_id) || empty($assignment) || empty($exam)) {
        http_response_code(400);
        echo json_encode(['error' => 'Student ID, assignment, and exam marks are required']);
        return;
    }

    // Check if student already has marks
    $checkStmt = $pdo->prepare("SELECT id FROM marks WHERE student_id = ?");
    $checkStmt->execute([$student_id]);
    if ($checkStmt->fetch()) {
        http_response_code(400);
        echo json_encode(['error' => 'This student already has marks. Each student can only have one mark entry.']);
        return;
    }

    // Calculate total: assignment (30%) + exam (70%)
    $total = ($assignment * 0.3) + ($exam * 0.7);

    $stmt = $pdo->prepare("INSERT INTO marks (student_id, assignment, exam, total) VALUES (?, ?, ?, ?)");

    if ($stmt->execute([$student_id, $assignment, $exam, $total])) {
        echo json_encode([
            'success' => true,
            'mark_id' => $pdo->lastInsertId(),
            'total' => $total
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to add marks']);
    }
}

function handleGetMarks($pdo)
{
    session_start();

    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Not authenticated']);
        return;
    }

    if ($_SESSION['role'] === 'student') {
        // Students can only see their own marks (assuming username matches student name)
        $stmt = $pdo->prepare("
            SELECT s.name, m.assignment, m.exam, m.total 
            FROM marks m 
            JOIN users s ON m.student_id = s.id 
            WHERE s.username = ?
        ");
        $stmt->execute([$_SESSION['username']]);
    } else {
        // Lecturers can see all marks
        $stmt = $pdo->prepare("
            SELECT s.name, s.username as matric_number, m.assignment, m.exam, m.total 
            FROM marks m 
            JOIN users s ON m.student_id = s.id 
            WHERE s.role = 'student'
            ORDER BY s.name
        ");
        $stmt->execute();
    }

    $marks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'marks' => $marks
    ]);
}

function handleGetStudents($pdo)
{
    session_start();

    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'lecturer') {
        http_response_code(403);
        echo json_encode(['error' => 'Unauthorized']);
        return;
    }

    $stmt = $pdo->prepare("SELECT id, username as matric_number, name FROM users WHERE role = 'student' ORDER BY name");
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'students' => $students
    ]);
}

function handleGetStudentsWithoutMarks($pdo) {
    session_start();
    
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'lecturer') {
        http_response_code(403);
        echo json_encode(['error' => 'Unauthorized']);
        return;
    }
    
    // Get students who don't have marks yet
    $stmt = $pdo->prepare("
        SELECT u.id, u.username as matric_number, u.name 
        FROM users u 
        WHERE u.role = 'student' 
        AND u.id NOT IN (SELECT DISTINCT student_id FROM marks)
        ORDER BY u.name
    ");
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'students' => $students
    ]);
}
?>