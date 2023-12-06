<?php

namespace App\Services;

use Throwable;
use App\Exceptions\CustomException;

class Application
{
    /**
     * Encrypt the password string
     */
    public function encryptPasswordString($text)
    {
        try {
            // Encryption
            $encrypted = openssl_encrypt($text, 'AES-128-CBC', config('fbn.password_encryption_key'), OPENSSL_RAW_DATA, config('fbn.password_encryption_initialization_vector'));
            $ciphertext = base64_encode($encrypted);

            return $ciphertext;
        } catch (Throwable $e) {
            throw new CustomException('Failed to encrypt password string: '.$e->getMessage(), 503);
        }
    }

    /**
     * Decrypt the password string
     */
    public function decryptPasswordString($encryptedText)
    {
        try {
            $decodedCiphertext = base64_decode($encryptedText);
            $text = openssl_decrypt($decodedCiphertext, 'AES-128-CBC', config('fbn.password_encryption_key'), OPENSSL_RAW_DATA, config('fbn.password_encryption_initialization_vector'));

            return $text;
        } catch (Throwable $e) {
            throw new CustomException('Failed to decrypt password string: '.$e->getMessage(), 503);
        }
    }
}