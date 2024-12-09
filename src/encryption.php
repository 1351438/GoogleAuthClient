<?php

class Encryption
{
    public $cipher = "AES-128-CBC";
    private $password;
    private $privateKey;

    public function __construct($privateKey, $password)
    {
        $this->password = $password;
        $this->privateKey = $privateKey;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }

    /**
     * @param mixed $privateKey
     */
    public function setPrivateKey($privateKey): void
    {
        $this->privateKey = $privateKey;
    }

    private function getIv()
    {
        $iv_length = openssl_cipher_iv_length($this->cipher);
        $iv = (base64_encode($this->password) . $this->password . base64_encode($this->password));
        return substr($iv, 0, $iv_length);
    }

    private function getKey()
    {
        return ($this->password);

    }

    public function decrypt($data)
    {
        $decryption = openssl_decrypt(base64_decode($data), $this->cipher, $this->getKey(), 0, $this->getIv());
        return ($decryption);
    }

    public function encrypt($plaintext)
    {
        $encryption = openssl_encrypt($plaintext, $this->cipher, $this->getKey(), 0, $this->getIv());
        return base64_encode($encryption);
    }
}