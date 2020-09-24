<?php
require '../vendor/autoload.php';
$config = include 'config.php';

use Omnipay\Omnipay;
use Omnipay\Borica\Gateway;

/** @var Gateway $gateway */
$gateway = Omnipay::create('Borica');
$gateway->setTerminalId($config['terminalId'])
    ->setPrivateKey($config['privateKey'])
    ->setCertificate($config['certificate'])
    ->setCurrency($config['currency'])
    ->setTestMode($config['testMode']);

$request = $gateway->purchase(
    [
        "amount" => "10",
        'order' => date('His'),
        'orderId' => uniqid(),
        "description" => "Borica Test Purchase",
        "merchantUrl" => "http://borica.way4",
        "returnUrl" => "http://borica.way4/return.php"
    ]
);

$response = $request->send();

// Process response
if ($response->isSuccessful()) {
    // Payment was successful
    print_r($response);
} elseif ($response->isRedirect()) {
    // Redirect to offsite payment gateway
    $response->redirect();
} else {
    // Payment failed
    echo $response->getMessage();
}
