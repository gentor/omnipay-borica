<?php


namespace Omnipay\Borica\Message;


use Omnipay\Borica\Signature;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Exception\InvalidResponseException;

class CompletePurchaseRequest extends FetchTransactionRequest
{
    const RETURN_RESPONSE_CODES = [-17, -25];

    protected $validTRTYPE = 1;

    protected $gatewayData = [];

    /**
     * @return array|mixed
     * @throws InvalidRequestException
     */
    public function getData()
    {
        parse_str($this->httpRequest->getContent(), $this->gatewayData);

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

        $valid = Signature::verify($data, $this->getGatewayCertificate());
        if ($valid < 1) {
            throw new InvalidRequestException("Invalid gateway signature (P_SIGN): " . $data['P_SIGN']);
        }
    }

    /**
     * @param mixed $data
     * @return FetchTransactionResponse
     * @throws InvalidRequestException
     * @throws InvalidResponseException
     */
    public function sendData($data)
    {
        $this->validateGatewayData();

        if (in_array($data['RC'], self::RETURN_RESPONSE_CODES)) {
            return $this->response = new FetchTransactionResponse($this, $data);
        }

        return parent::sendData($data);
    }
}