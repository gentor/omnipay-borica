<?php


namespace Omnipay\Borica\Message;


use Omnipay\Common\Message\AbstractResponse;

class FetchTransactionResponse extends AbstractResponse
{
    public function getData()
    {
        $this->data['isReversal'] = $this->isReversal();

        return $this->data;
    }

    public function isReversal()
    {
        return $this->data['TRTYPE'] == RefundRequest::TR_TYPE;
    }

    public function isSuccessful()
    {
        return $this->getCode() === '00';
    }

    public function getCode()
    {
        return $this->data['RC'] ?? null;
    }

    public function getMessage()
    {
        return $this->data['STATUSMSG'] ?? null;
    }

    public function getTransactionId()
    {
        return $this->data['NONCE'] ?? null;
    }

    public function getTransactionReference()
    {
        return $this->data['INT_REF'] ?? null;
    }
}