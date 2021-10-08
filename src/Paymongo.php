<?php

namespace jamesclavel\paymongo;

// use Dotenv\Dotenv;

class Paymongo
{
    private $keyPublic;
    private $keySecret;

    public function __construct() {
        $this->keyPublic = $_ENV['PAYMONOGO_PUBLIC_KEY'];
        $this->keySecret = $_ENV['PAYMONOGO_SECRET_KEY'];
    }

    public function getPublicKey()
    {
        return $_ENV['PAYMONOGO_PUBLIC_KEY'];
    }
    
    public function getSecretKey()
    {
        return $_ENV['PAYMONOGO_SECRET_KEY'];
    }
}
