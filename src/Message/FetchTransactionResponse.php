<?php


namespace Omnipay\Borica\Message;


use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\ResponseInterface;

class FetchTransactionResponse extends AbstractResponse implements ResponseInterface
{
    public function isSuccessful()
    {
        return $this->getCode() === '00';
    }

    public function getCode()
    {
        return $this->data['responseCode'] ?? null;
    }

    public function getMessage()
    {
        return $this->data['statusMsg'] ?? null;
    }

    public function getTransactionId()
    {
        return $this->data['nonce'] ?? null;
    }

    public function getTransactionReference()
    {
        return $this->data['intRef'] ?? null;
    }
}