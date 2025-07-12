<?php

use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../src/AuthController.php';
require __DIR__ . '/../src/MarkController.php';

// Create Slim app
$app = AppFactory::create();

// Add middleware to parse JSON
$app->addBodyParsingMiddleware();

// Add CORS middleware
$app->add(function (Request $request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

// Authentication routes
$app->post('/login', function (Request $request, Response $response) {
    $auth = new \App\AuthController();
    return $auth->login($request, $response);
});

$app->post('/logout', function (Request $request, Response $response) {
    $auth = new \App\AuthController();
    return $auth->logout($request, $response);
});

$app->get('/check-auth', function (Request $request, Response $response) {
    $auth = new \App\AuthController();
    return $auth->checkAuth($request, $response);
});

// Mark management routes
$app->post('/add-student', function (Request $request, Response $response) {
    $markController = new \App\MarkController();
    return $markController->addStudent($request, $response);
});

$app->post('/add-marks', function (Request $request, Response $response) {
    $markController = new \App\MarkController();
    return $markController->addMarks($request, $response);
});

$app->get('/get-marks', function (Request $request, Response $response) {
    $markController = new \App\MarkController();
    return $markController->getMarks($request, $response);
});

$app->get('/get-students', function (Request $request, Response $response) {
    $markController = new \App\MarkController();
    return $markController->getStudents($request, $response);
});

// Health check route
$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write(json_encode(['status' => 'API is running']));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run(); 