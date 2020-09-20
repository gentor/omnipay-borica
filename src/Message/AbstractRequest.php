<?php


namespace Omnipay\Borica\Message;


use Omnipay\Borica\Signature;

abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    protected $liveEndpoint = 'https://3dsgate.borica.bg/cgi-bin/cgi_link';
    protected $testEndpoint = 'https://3dsgate-dev.borica.bg/cgi-bin/cgi_link';
//    protected $testEndpoint = 'https://gateowt.borica.bg:8443/cgi-bin/cgi_link';

    protected $macFields = [];

    public function getData()
    {
        return [
            'TERMINAL' => $this->getTerminalId(),
            'CURRENCY' => $this->getCurrency(),
            'TIMESTAMP' => gmdate('YmdHis'),
            'NONCE' => bin2hex(microtime(true)),
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

    protected function sign($data)
    {
        $message = Signature::getMacSourceValue($data);

        return Signature::create($message, $this->getPrivateKey());
    }

    public function getEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }

}