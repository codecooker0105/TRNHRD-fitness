<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '../vendor/autoload.php'; // Load Composer autoloader

use Firebase\JWT\JWT;

class Jwt_creator {
    private $privateKeyContent;

    public function __construct() {
        $this->privateKeyContent = <<<EOD
        -----BEGIN PRIVATE KEY-----
        MIGTAgEAMBMGByqGSM49AgEGCCqGSM49AwEHBHkwdwIBAQQguHI7qLWvc2Ep+b6s
        RpioQ5jFP0r4qmvAbLTKeBa4cH6gCgYIKoZIzj0DAQehRANCAARBnKrFta4WzzAh
        qNu/l3TXlGhdhFsFoQsrWA1FYtEMAMVo4a5meeJgECG9Zdc5fbYnoCELc4a61Wit
        1R4kZV20
        -----END PRIVATE KEY-----
        EOD;
    }

    public function createToken($payload) {
        $headers = array(
            "kid" => "545ACZPJ79", // Key ID from your .p8 private key file
        );

        return JWT::encode($payload, $this->privateKeyContent, 'ES256', null, $headers);
    }
}
