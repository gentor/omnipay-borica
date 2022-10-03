<?php

namespace Omnipay\Borica\Message;

trait SigningScheme
{
    public static $ALGO = OPENSSL_ALGO_SHA256;

    public static $requestMacFields = [
        'MAC_ADVANCED' => [
            'default' => [
                'TERMINAL', 'TRTYPE',
                'AMOUNT', 'CURRENCY',
                'ORDER',
                'TIMESTAMP', 'NONCE',
            ],
            90 => [
                'TERMINAL', 'TRTYPE',
                'ORDER', 'NONCE',
            ],
        ],
        'MAC_GENERAL' => [
            'default' => [
                'TERMINAL', 'TRTYPE',
                'AMOUNT', 'CURRENCY',
                'ORDER',
                'TIMESTAMP', 'NONCE',
                'MERCH_TOKEN_ID',
            ],
            90 => [
                'TERMINAL', 'TRTYPE',
                'ORDER', 'NONCE',
            ],
        ],
    ];

    public static $responseMacFields = [
        'MAC_ADVANCED' => [
            'default' => [
                'ACTION', 'RC', 'APPROVAL', 'TERMINAL', 'TRTYPE',
                'AMOUNT', 'CURRENCY', 'ORDER', 'RRN',
                'INT_REF', 'PARES_STATUS', 'ECI', 'TIMESTAMP',
                'NONCE',
            ],
        ],
        'MAC_GENERAL' => [
            'default' => [
                'ACTION', 'RC', 'APPROVAL', 'TERMINAL', 'TRTYPE',
                'AMOUNT', 'CURRENCY', 'ORDER', 'RRN',
                'INT_REF', 'PARES_STATUS', 'ECI', 'TIMESTAMP',
                'NONCE', 'MERCH_TOKEN_ID',
            ],
        ],
    ];

    public function getSignScheme()
    {
        return $this->getParameter('signScheme');
    }

    public function setSignScheme($value)
    {
        return $this->setParameter('signScheme', $value);
    }

    public function getMacSourceValue(array $data, $isResponse = false)
    {
        $scheme = $this->getSignScheme();
        $type = $data['TRTYPE'];
        $macFields = $isResponse ? self::$responseMacFields : self::$requestMacFields;
        $macFields = $macFields[$scheme][$type] ?? $macFields[$scheme]['default'];

        $message = '';

        foreach ($macFields as $field) {
            if (($data[$field] ?? '') == '') {
                $message .= '-';
            } else {
                $message .= strlen($data[$field]) . $data[$field];
            }
        }

        return $message;
    }

    public function createSignature($message)
    {
        $privateKeyId = openssl_get_privatekey($this->getPrivateKey());

        openssl_sign($message, $signature, $privateKeyId, self::$ALGO);
        openssl_free_key($privateKeyId);

        return strtoupper(bin2hex($signature));
    }

    public function verifySignature(array $data)
    {
        if (empty($data['P_SIGN'])) {
            return -1;
        }

        $message = $this->getMacSourceValue($data, true);
        $signature = hex2bin($data['P_SIGN']);
        $publicKeyId = openssl_get_publickey($this->getGatewayCertificate());

        return openssl_verify($message, $signature, $publicKeyId, self::$ALGO);
    }

}