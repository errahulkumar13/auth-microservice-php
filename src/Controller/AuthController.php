<?php
namespace App\Controller;

use App\Model\User;
use App\Service\TokenService;

class AuthController {
    private $userModel;
    private $tokenService;

    public function __construct($pdo, $secret) {
        $this->userModel = new User($pdo);
        $this->tokenService = new TokenService($secret);
    }

    public function login($data) {
        $user = $this->userModel->findByEmail($data['email']);

        if ($user && password_verify($data['password'], $user['password'])) {
            $token = $this->tokenService->generate([
                'user_id' => $user['id'],
                'email'   => $user['email']
            ]);
            return json_encode(['token' => $token]);
        }

        http_response_code(401);
        return json_encode(['message' => 'Invalid credentials']);
    }

    public function validateToken($decodedToken) {
        return json_encode([
            'valid' => true,
            'user' => $decodedToken
        ]);
    }
}