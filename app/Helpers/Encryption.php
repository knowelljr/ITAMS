<?php

namespace App\Helpers;

class Encryption
{
    /**
     * Hash a password using bcrypt
     */
    public static function hashPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Verify a password against a hash
     */
    public static function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * Encrypt a string using AES-256-CBC
     */
    public static function encrypt($data)
    {
        $key = $_ENV['ENCRYPTION_KEY'] ?? 'default_key_32_characters_long!!! ';
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
        return base64_encode($iv . $encrypted);
    }

    /**
     * Decrypt a string using AES-256-CBC
     */
    public static function decrypt($data)
    {
        $key = $_ENV['ENCRYPTION_KEY'] ?? 'default_key_32_characters_long!!!';
        $data = base64_decode($data);
        $iv = substr($data, 0, openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = substr($data, openssl_cipher_iv_length('aes-256-cbc'));
        return openssl_decrypt($encrypted, 'aes-256-cbc', $key, 0, $iv);
    }
}
?>