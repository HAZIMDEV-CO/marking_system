<?php

namespace App;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class MarkController {
    private $db;

    public function __construct() {
        $this->db = new \Database();
    }

    public function addStudent(Request $request, Response $response): Response {
        session_start();
        
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'lecturer') {
            return $response->withJson(['error' => 'Unauthorized'], 403);
        }

        $data = $request->getParsedBody();
        $name = $data['name'] ?? '';

        if (empty($name)) {
            return $response->withJson(['error' => 'Student name is required'], 400);
        }

        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("INSERT INTO students (name) VALUES (?)");
        
        if ($stmt->execute([$name])) {
            return $response->withJson([
                'success' => true,
                'student_id' => $conn->lastInsertId()
            ]);
        }

        return $response->withJson(['error' => 'Failed to add student'], 500);
    }

    public function addMarks(Request $request, Response $response): Response {
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

    public function getMarks(Request $request, Response $response): Response {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            return $response->withJson(['error' => 'Not authenticated'], 401);
        }

        $conn = $this->db->getConnection();
        
        if ($_SESSION['role'] === 'student') {
            // Students can only see their own marks (assuming username matches student name)
            $stmt = $conn->prepare("
                SELECT s.name, m.assignment, m.exam, m.total 
                FROM marks m 
                JOIN students s ON m.student_id = s.id 
                WHERE s.name = ?
            ");
            $stmt->execute([$_SESSION['username']]);
        } else {
            // Lecturers can see all marks
            $stmt = $conn->prepare("
                SELECT s.name, m.assignment, m.exam, m.total 
                FROM marks m 
                JOIN students s ON m.student_id = s.id 
                ORDER BY s.name
            ");
            $stmt->execute();
        }

        $marks = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        return $response->withJson([
            'success' => true,
            'marks' => $marks
        ]);
    }

    public function getStudents(Request $request, Response $response): Response {
        session_start();
        
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'lecturer') {
            return $response->withJson(['error' => 'Unauthorized'], 403);
        }

        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("SELECT id, name FROM students ORDER BY name");
        $stmt->execute();
        $students = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $response->withJson([
            'success' => true,
            'students' => $students
        ]);
    }
} 