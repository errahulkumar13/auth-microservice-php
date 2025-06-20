<?php
namespace App\Service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class TokenService {
    private $secret;

    public function __construct($secret) {
        $this->secret = $secret;
    }

    public function generate($payload, $expMinutes = 15) {
        $payload['exp'] = time() + (60 * $expMinutes);
        return JWT::encode($payload, $this->secret, 'HS256');
    }

    public function validate($token) {
        return JWT::decode($token, new Key($this->secret, 'HS256'));
    }
}