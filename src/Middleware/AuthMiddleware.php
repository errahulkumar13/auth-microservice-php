<?php
namespace App\Middleware;

use App\Service\TokenService;

class AuthMiddleware {
    private $tokenService;

    public function __construct($secret) {
        $this->tokenService = new TokenService($secret);
    }

    public function handle($callback) {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            http_response_code(401);
            echo json_encode(['message' => 'Unauthorized: Missing or invalid Authorization header']);
            exit;
        }

        $token = str_replace('Bearer ', '', $authHeader);

        try {
            $decoded = $this->tokenService->validate($token);
            // Optionally: pass decoded payload to callback
            $callback($decoded);
        } catch (\Exception $e) {
            http_response_code(401);
            echo json_encode(['message' => 'Unauthorized: ' . $e->getMessage()]);
            exit;
        }
    }
}