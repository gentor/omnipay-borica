<?php


namespace Omnipay\Borica\Message;


use Omnipay\Borica\Signature;
use Omnipay\Common\Exception\InvalidRequestException;

abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    protected $liveEndpoint = 'https://3dsgate.borica.bg/cgi-bin/cgi_link';
    protected $testEndpoint = 'https://3dsgate-dev.borica.bg/cgi-bin/cgi_link';

    public function getData()
    {
        $this->validate('terminalId', 'privateKey');
        $this->validatePrivateKey();
        $this->validateCertificate();

        return [
            'TERMINAL' => $this->getTerminalId(),
            'TIMESTAMP' => gmdate('YmdHis'),
        ];
    }

    public function getTerminalId()
    {
        return $this->getParameter('terminalId');
    }

    public function setTerminalId($value)
    {
        return $this->setParameter('terminalId', $value);
    }

    public function getPrivateKey()
    {
        return $this->getParameter('privateKey');
    }

    public function setPrivateKey($value)
    {
        return $this->setParameter('privateKey', $value);
    }

    public function getCertificate()
    {
        return $this->getParameter('certificate');
    }

    public function setCertificate($value)
    {
        return $this->setParameter('certificate', $value);
    }

    public function getGatewayCertificate()
    {
        return $this->getParameter('gatewayCertificate');
    }

    public function setGatewayCertificate($value)
    {
        return $this->setParameter('gatewayCertificate', $value);
    }

    protected function sign($data)
    {
        $message = Signature::getMacSourceValue($data);

        return Signature::create($message, $this->getPrivateKey());
    }

    public function getEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }

    public function validatePrivateKey()
    {
        $result = openssl_get_privatekey($this->getPrivateKey());
        if (!$result) {
            throw new InvalidRequestException("The privateKey parameter is invalid");
        }
    }

    public function validateCertificate()
    {
        $result = Signature::parseCertificate($this->getCertificate());
        if (!$result) {
            throw new InvalidRequestException("The certificate parameter is invalid");
        }

        if ($result['isExpired']) {
            throw new InvalidRequestException("The certificate expired on " . $result['validTo']);
        }

        if (!Signature::checkCertificatePrivateKey($this->getCertificate(), $this->getPrivateKey())) {
            throw new InvalidRequestException("The privateKey does not corresponds to the certificate");
        }
    }
}