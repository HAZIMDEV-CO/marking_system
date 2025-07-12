<?php

namespace App;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthController {
    private $db;

    public function __construct() {
        $this->db = new \Database();
    }

    public function login(Request $request, Response $response): Response {
        $data = $request->getParsedBody();
        
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            return $response->withJson(['error' => 'Username and password are required'], 400);
        }

        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Start session
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            return $response->withJson([
                'success' => true,
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'role' => $user['role']
                ]
            ]);
        }

        return $response->withJson(['error' => 'Invalid credentials'], 401);
    }

    public function logout(Request $request, Response $response): Response {
        session_start();
        session_destroy();
        
        return $response->withJson(['success' => true]);
    }

    public function checkAuth(Request $request, Response $response): Response {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            return $response->withJson(['error' => 'Not authenticated'], 401);
        }

        return $response->withJson([
            'user' => [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'role' => $_SESSION['role']
            ]
        ]);
    }
} 