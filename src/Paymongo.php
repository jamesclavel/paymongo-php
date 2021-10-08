<?php

namespace jamesclavel\paymongo;

use Dotenv\Dotenv;
use GuzzleHttp\Client;

class Paymongo
{
    private $paymongoApiUrl = 'https://api.paymongo.com';
    private $keyPublic;
    private $keySecret;
    private $client;

    public function __construct() {
        Dotenv::createImmutable($_SERVER['DOCUMENT_ROOT'])->load();

        $this->keyPublic = $_ENV['PAYMONGO_PUBLIC_KEY'];
        $this->keySecret = $_ENV['PAYMONGO_SECRET_KEY'];

        $this->client = new Client([
            'base_uri' => $this->paymongoApiUrl,
            'timeout'  => 3.0,
            'headers' => [
                'Authorization' => 'Basic '.base64_encode($this->keySecret),
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ]
        ]);
    }

    public function getPublicKey()
    {
        return $this->keyPublic;
    }
    
    public function getSecretKey()
    {
        return $this->keySecret;
    }

    public function getPaymentMethod($paymentMethodId=null)
    {
        $paymentMethod = $this->client->get("/v1/payment_methods/$paymentMethodId");
        if ($paymentMethod->getStatusCode() == 200) {
            $body = $paymentMethod->getBody();
            return $body->getContents();
        } else {
            return false;
        }
    }

    public function createPaymentIntent($amount, $description, $statementDescriptor, $metadata=[])
    {
        $data = [
            'data' => [
                'attributes' => [
                    'amount' => intval($amount),
                    'payment_method_allowed' => ['card'],
                    'payment_method_options' => [
                        'card' => [
                            'request_three_d_secure' => 'any'
                        ]
                    ],
                    'description' => $description,
                    'statement_descriptor' => $statementDescriptor,
                    'currency' => 'PHP',
                ]
            ]
        ];

        if (!empty($metadata)) {
            $data['data']['attributes']['metadata'] = $metadata;
        }

        $paymentIntent = $this->client->post("/v1/payment_intents", [
            'json' => $data
        ]);

        if ($paymentIntent->getStatusCode() == 200) {
            $body = $paymentIntent->getBody();
            return $body->getContents();
        } else {
            return false;
        }
    }

    public function getPaymentIntent($paymentIntentId)
    {
        $paymentIntent = $this->client->get("/v1/payment_intents/$paymentIntentId");

        if ($paymentIntent->getStatusCode() == 200) {
            $body = $paymentIntent->getBody();
            return $body->getContents();
        } else {
            return false;
        }
    }

    public function attachPaymentMethodToPaymentIntent($paymentIntent, $paymentMethodId, $clientKey)
    {
        $data = [
            'data' => [
                'attributes' => [
                    'payment_method' => $paymentMethodId,
                    'client_key' => $clientKey,
                    'return_url' => 'https://paymongo.test/',
                ]
            ]
        ];
        $paymentIntent = $this->client->post("/v1/payment_intents/$paymentIntent/attach", [
            'json' => $data
        ]);

        if ($paymentIntent->getStatusCode() == 200) {
            $body = $paymentIntent->getBody();
            return $body->getContents();
        } else {
            return false;
        }
    }
}
