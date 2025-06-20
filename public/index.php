<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\AuthController;
use App\Middleware\AuthMiddleware;

// Load DB and secret
$pdo = require_once __DIR__ . '/../config/database.php';
$secret = $_ENV['JWT_SECRET'];

// Initialize controller and middleware
$controller = new AuthController($pdo, $secret);
$authMiddleware = new AuthMiddleware($secret);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

header('Content-Type: application/json');

if ($uri === '/login' && $method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    echo $controller->login($input);

} elseif ($uri === '/validate-token' && $method === 'GET') {
    // Use middleware to validate JWT
    $authMiddleware->handle(function ($decodedToken) use ($controller) {
        echo $controller->validateToken($decodedToken);
    });

} elseif ($uri === '/protected' && $method === 'GET') {
    $authMiddleware->handle(function ($decodedToken) {
        echo json_encode([
            'message' => 'You have accessed a protected route!',
            'user' => $decodedToken
        ]);
    });

} else {
    http_response_code(404);
    echo json_encode(['message' => 'Not Found']);
}
