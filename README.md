# Omnipay: Borica

**[Borica Way4](https://www.openwaygroup.com/new-blog/2018/11/1/the-migration-to-the-way4-card-payment-system-is-underway) gateway for Omnipay payment processing library**

[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP. This package implements Borica Way4 support for Omnipay.

## Installation

Omnipay is installed via [Composer](http://getcomposer.org/). To install, simply require `league/omnipay` and `gentor/omnipay-borica` with Composer:

```
composer require league/omnipay gentor/omnipay-borica
```

## Basic Usage

### Purchase
```php
$gateway = Omnipay::create('Borica');

$gateway->setTerminalId($config['terminalId'])
    ->setPrivateKey($config['privateKey'])
    ->setCertificate($config['certificate'])
    ->setCurrency($config['currency'])
    ->setTestMode($config['testMode']);

$response = $gateway->purchase(
    [
        "amount" => "10",
        'order' => date('His'),
        'orderId' => uniqid(),
        "description" => "Borica Test Purchase",
        "merchantUrl" => "http://borica.way4",
        "returnUrl" => "http://borica.way4/return.php"
    ]
)->send();

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
```

### Complete Purchase
```php
$response = $gateway->completePurchase($_POST)->send();

var_dump($response->getData());
var_dump($response->isSuccessful());
var_dump($response->getCode());
var_dump($response->getMessage());

```

### Refund
```php
$response = $gateway->refund([
    'order' => date('His'),
    'transactionType' => 1, // original transaction TRTYPE
    'amount' => 10,
    'currency' => 'BGN',
    'description' => 'Borica Test Purchase',
    'RRN' => 'RRN',
    'INT_REF' => 'INT_REF',
])->send();

var_dump($response->getData());
var_dump($response->isSuccessful());
var_dump($response->getCode());
var_dump($response->getMessage());

```

### Fetch Transaction
```php
$response = $gateway->fetchTransaction([
    'order' => date('His'),
    'nonce' => 'nonce', // original transaction NONCE
    'transactionType' => 1, // original transaction TRTYPE
])->send();

var_dump($response->getData());
var_dump($response->isSuccessful());
var_dump($response->isReversal());
var_dump($response->getCode());
var_dump($response->getMessage());
var_dump($response->getTransactionReference());

```
