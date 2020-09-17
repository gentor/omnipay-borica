<?php


namespace Omnipay\Borica\Message;


abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    protected $liveEndpoint = 'https://3dsgate.borica.bg';
    protected $testEndpoint = 'https://3dsgate-dev.borica.bg';

    public function getData()
    {
        // TODO: Implement getData() method.
    }

    public function sendData($data)
    {
        // TODO: Implement sendData() method.
    }

    protected function getEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }

}