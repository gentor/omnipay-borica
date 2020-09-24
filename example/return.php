<?php

use Omnipay\Borica\Gateway;
use Omnipay\Omnipay;

require '../vendor/autoload.php';
$config = include 'config.php';

/** @var Gateway $gateway */
$gateway = Omnipay::create('Borica');
$gateway->setTerminalId($config['terminalId'])
    ->setPrivateKey($config['privateKey'])
    ->setCertificate($config['certificate'])
    ->setCurrency($config['currency'])
//    ->setGatewayCertificate(null)
    ->setTestMode($config['testMode']);

echo '<pre>';

$request = $gateway->completePurchase($_POST);
var_dump($request->getGatewayData());
var_dump($request->getData());

$response = $request->send();
var_dump($response->getData());
var_dump($response->isSuccessful());
var_dump($response->getCode());
var_dump($response->getMessage());
