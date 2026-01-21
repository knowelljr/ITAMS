<?php

namespace App\Helpers;

use Firebase\JWT\JWT as FirebaseJWT;
use Firebase\JWT\Key;

class JWT
{
    private $secret;
    private $algo = 'HS256';
    private $expiry;

    public function __construct()
    {
        $this->secret = $_ENV['JWT_SECRET'] ?? '';
        $this->expiry = $_ENV['JWT_EXPIRY'] ?? 86400;
        
        if (empty($this->secret)) {
            throw new \Exception('JWT_SECRET not set in .env');
        }
    }

    public function encode($data)
    {
        $issuedAt = time();
        $expire = $issuedAt + $this->expiry;

        $payload = array_merge($data, [
            'iat' => $issuedAt,
            'exp' => $expire,
        ]);

        return FirebaseJWT::encode($payload, $this->secret, $this->algo);
    }

    public function decode($token)
    {
        try {
            return FirebaseJWT::decode($token, new Key($this->secret, $this->algo));
        } catch (\Exception $e) {
            return null;
        }
    }

    public function verify($token)
    {
        return $this->decode($token) !== null;
    }
}

?>