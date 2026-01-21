<?php

namespace App\Helpers;

class Encryption
{
    private $key;
    private $algorithm = 'AES-256-CBC';

    public function __construct()
    {
        $this->key = getenv('ENCRYPTION_KEY') ?: 'default-encryption-key-change-in-production';
    }

    public function encrypt($plaintext)
    {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->algorithm));
        $encrypted = openssl_encrypt($plaintext, $this->algorithm, $this->key, 0, $iv);
        return base64_encode($iv . $encrypted);
    }

    public function decrypt($ciphertext)
    {
        $data = base64_decode($ciphertext);
        $iv = substr($data, 0, openssl_cipher_iv_length($this->algorithm));
        $encrypted = substr($data, openssl_cipher_iv_length($this->algorithm));
        return openssl_decrypt($encrypted, $this->algorithm, $this->key, 0, $iv);
    }

    public function hashPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    public function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }
}