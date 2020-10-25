<?php


namespace Omnipay\Borica\Message;


use Omnipay\Borica\Signature;
use Omnipay\Common\Exception\InvalidRequestException;

class CompletePurchaseRequest extends FetchTransactionRequest
{
    protected $validTRTYPE = 1;

    protected $gatewayData = [];

    /**
     * @return array|mixed
     * @throws InvalidRequestException
     */
    public function getData()
    {
        parse_str($this->httpRequest->getContent(), $this->gatewayData);

        $this->validateGatewayData();

        return parent::getData();
    }

    public function setTRTYPE($value)
    {
        $this->setTransactionType($value);
    }

    public function getGatewayData()
    {
        return $this->gatewayData;
    }

    /**
     * @throws InvalidRequestException
     */
    public function validateGatewayData()
    {
        if ($this->gatewayData['TRTYPE'] != $this->validTRTYPE) {
            throw new InvalidRequestException("Invalid TRTYPE: {$this->gatewayData['TRTYPE']}, expected value is {$this->validTRTYPE}");
        }

        $this->validateGatewaySignature($this->gatewayData);
    }

    /**
     * @param array $data
     * @throws InvalidRequestException
     */
    public function validateGatewaySignature(array $data)
    {
        if (!$this->getGatewayCertificate()) {
            return;
        }

        $valid = Signature::verify($data, $this->getGatewayCertificate(), $this->getSignScheme());
        if ($valid < 1) {
            throw new InvalidRequestException("Invalid gateway signature (P_SIGN): " . $data['P_SIGN']);
        }
    }
}