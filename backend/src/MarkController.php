<?php

namespace App;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class MarkController
{
    private $db;

    public function __construct()
    {
        $this->db = new \Database();
    }

    public function addStudent(Request $request, Response $response): Response
    {
        session_start();

        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'lecturer') {
            return $response->withJson(['error' => 'Unauthorized'], 403);
        }

        $data = $request->getParsedBody();
        $name = $data['name'] ?? '';
        $matricNumber = $data['matric_number'] ?? '';

        if (empty($name) || empty($matricNumber)) {
            return $response->withJson(['error' => 'Student name and matric number are required'], 400);
        }

        // Validate matric number format (you can adjust this validation as needed)
        if (!preg_match('/^\d{7}$/', $matricNumber)) {
            return $response->withJson(['error' => 'Matric number must be 7 digits'], 400);
        }

        $conn = $this->db->getConnection();

        // Check if matric number already exists
        $checkStmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $checkStmt->execute([$matricNumber]);
        if ($checkStmt->fetch()) {
            return $response->withJson(['error' => 'Student with this matric number already exists'], 400);
        }

        // Hash the password (matric number as password)
        $hashedPassword = password_hash($matricNumber, PASSWORD_DEFAULT);

        // Insert new student into users table
        $stmt = $conn->prepare("INSERT INTO users (username, password, role, name) VALUES (?, ?, 'student', ?)");

        if ($stmt->execute([$matricNumber, $hashedPassword, $name])) {
            return $response->withJson([
                'success' => true,
                'student_id' => $conn->lastInsertId(),
                'message' => "Student added successfully. Username: $matricNumber, Password: $matricNumber"
            ]);
        }

        return $response->withJson(['error' => 'Failed to add student'], 500);
    }

    public function addMarks(Request $request, Response $response): Response
    {
        session_start();

        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'lecturer') {
            return $response->withJson(['error' => 'Unauthorized'], 403);
        }

        $data = $request->getParsedBody();
        $student_id = $data['student_id'] ?? '';
        $assignment = $data['assignment'] ?? '';
        $exam = $data['exam'] ?? '';

        if (empty($student_id) || empty($assignment) || empty($exam)) {
            return $response->withJson(['error' => 'Student ID, assignment, and exam marks are required'], 400);
        }

        // Calculate total: assignment (30%) + exam (70%)
        $total = ($assignment * 0.3) + ($exam * 0.7);

        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("INSERT INTO marks (student_id, assignment, exam, total) VALUES (?, ?, ?, ?)");

        if ($stmt->execute([$student_id, $assignment, $exam, $total])) {
            return $response->withJson([
                'success' => true,
                'mark_id' => $conn->lastInsertId(),
                'total' => $total
            ]);
        }

        return $response->withJson(['error' => 'Failed to add marks'], 500);
    }

    public function getMarks(Request $request, Response $response): Response
    {
        session_start();

        if (!isset($_SESSION['user_id'])) {
            return $response->withJson(['error' => 'Not authenticated'], 401);
        }

        $conn = $this->db->getConnection();

        if ($_SESSION['role'] === 'student') {
            // Students can only see their own marks
            $stmt = $conn->prepare("
                SELECT u.name, m.assignment, m.exam, m.total 
                FROM marks m 
                JOIN users u ON m.student_id = u.id 
                WHERE u.id = ?
            ");
            $stmt->execute([$_SESSION['user_id']]);
        } else {
            // Lecturers can see all marks
            $stmt = $conn->prepare("
                SELECT u.name, u.username as matric_number, m.assignment, m.exam, m.total 
                FROM marks m 
                JOIN users u ON m.student_id = u.id 
                WHERE u.role = 'student'
                ORDER BY u.name
            ");
            $stmt->execute();
        }

        $marks = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $response->withJson([
            'success' => true,
            'marks' => $marks
        ]);
    }

    public function getStudents(Request $request, Response $response): Response
    {
        session_start();

        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'lecturer') {
            return $response->withJson(['error' => 'Unauthorized'], 403);
        }

        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("SELECT id, username as matric_number, name FROM users WHERE role = 'student' ORDER BY name");
        $stmt->execute();
        $students = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $response->withJson([
            'success' => true,
            'students' => $students
        ]);
    }
}