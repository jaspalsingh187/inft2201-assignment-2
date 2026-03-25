<?php
namespace Application;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Verifier
{
    public $userId;
    public $role;

    public function decode($jwt) 
    {   
        $this->userId = null;
        $this->role = null;

        if (!empty($jwt)) {
            $jwt = trim($jwt);

            if (substr($jwt, 0, 7) === 'Bearer ') {
                $jwt = substr($jwt, 7);
            }

            try {
                $token = JWT::decode($jwt, new Key("jaspal_random_secret_2026_8472", 'HS256'));
                $this->userId = $token->userId;
                $this->role = $token->role;
            } catch (\Throwable $e) {
            }
        }
    }
}