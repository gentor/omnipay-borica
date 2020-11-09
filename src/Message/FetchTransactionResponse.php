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
        if (isset($this->data['RC'])) {
            return $this->data['RC'];
        }

        // TODO: remove this when v2.2 id deprecated
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