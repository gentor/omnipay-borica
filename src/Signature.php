<?php


namespace Omnipay\Borica;


use DateTime;

class Signature
{
    const ALGO = OPENSSL_ALGO_SHA256;

    public static $csrFields = [
        'commonName',
        'countryName',
        'localityName',
        'emailAddress',
        'organizationName',
        'stateOrProvinceName',
        'organizationalUnitName',
    ];

    /**
     * Generate 2048 bit RSA private and public keys
     *
     * @return array
     */
    public static function generateKeyPair()
    {
        $key = openssl_pkey_new(array(
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ));

        $details = openssl_pkey_get_details($key);
        openssl_pkey_export($key, $privateKey);

        return [
            'bits' => $details['bits'],
            'type' => 'RSA-SHA256',
            'public' => $details['key'],
            'private' => $privateKey,
        ];
    }

    /**
     * Generate Certificate Signing Request
     *
     * @param $privateKey
     * @param $terminalId
     * @param $domain
     * @param string $organization
     * @param string $state
     * @return array
     */
    public static function generateCSR($privateKey, $terminalId, $domain, array $subject = ['organizationName' => 'Omnipay Borica'])
    {
        $dn = [
            'organizationalUnitName' => $terminalId,
            'commonName' => $domain,
            'countryName' => 'BG',
            'localityName' => 'BG',
            'stateOrProvinceName' => 'BG',
        ];

        foreach ($subject as $key => $value) {
            if (in_array($key, self::$csrFields)) {
                $dn[$key] = $value;
            }
        }

        $privkey = openssl_get_privatekey($privateKey);
        $resource = openssl_csr_new($dn, $privkey, ['digest_alg' => self::ALGO]);

        openssl_csr_export($resource, $csr);

        return array_merge(
            [
                'csr' => $csr,
            ],
            self::parseCSR($csr)
        );
    }

    public static function parseCSR($certificate)
    {
        $subject = openssl_csr_get_subject($certificate, false);
        $publicKey = openssl_csr_get_public_key($certificate);
        $publicKey = openssl_pkey_get_details($publicKey);

        return [
            'subject' => $subject,
            'key' => [
                'bits' => $publicKey['bits'],
                'type' => $publicKey['type'] === 0 ? 'RSA-SHA256' : $publicKey['type'],
                'public' => $publicKey['key'],
            ],
        ];
    }

    public static function parseCertificate($certificate)
    {
//        $parsed = openssl_x509_read($certificate);
        $parsed = openssl_x509_parse($certificate, false);
        if ($parsed === false) {
            return false;
        }

        $publicKey = openssl_get_publickey($certificate);
        $details = openssl_pkey_get_details($publicKey);
        $publicKey = [
            'bits' => $details['bits'],
            'type' => $parsed['signatureTypeSN'],
            'public' => $details['key'],
        ];

        return [
//            'parsed' => $parsed,
            'subject' => $parsed['subject'],
            'issuer' => $parsed['issuer'],
            'validFrom' => date(DateTime::ISO8601, $parsed['validFrom_time_t']),
            'validTo' => date(DateTime::ISO8601, $parsed['validTo_time_t']),
            'isExpired' => $parsed['validTo_time_t'] < time(),
            'serialNumberHex' => $parsed['serialNumberHex'],
            'fingerprint' => [
                'md5' => wordwrap(strtoupper(openssl_x509_fingerprint($certificate, 'md5')), 2, ':', true),
                'sha1' => wordwrap(strtoupper(openssl_x509_fingerprint($certificate, 'sha1')), 2, ':', true),
                'sha256' => wordwrap(strtoupper(openssl_x509_fingerprint($certificate, 'sha256')), 2, ':', true),
            ],
            'key' => $publicKey,
        ];
    }

    public static function checkCertificatePrivateKey($certificate, $key)
    {
        return openssl_x509_check_private_key($certificate, $key);
    }
}