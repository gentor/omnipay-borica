<?php


namespace Omnipay\Borica\Message;


use Omnipay\Borica\Signature;
use Omnipay\Common\Exception\InvalidRequestException;

class CompletePurchaseRequest extends FetchTransactionRequest
{
    protected $validTRTYPE = 1;

    protected $gatewayFields = [
        'TERMINAL',
        'TRTYPE',
        'ORDER',
        'AMOUNT',
        'CURRENCY',
        'ACTION',
        'RC',
        'APPROVAL',
        'RRN',
        'INT_REF',
        'TIMESTAMP',
        'NONCE',
        'P_SIGN',
    ];

    protected $gatewayData = [];

    /**
     * @return array|mixed
     * @throws InvalidRequestException
     */
    public function getData()
    {
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
        foreach ($this->gatewayFields as $gatewayField) {
            if (!array_key_exists($gatewayField, $this->gatewayData)) {
                throw new InvalidRequestException("Missing $gatewayField parameter");
            }
        }

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

    public function initialize(array $parameters = array())
    {
        foreach ($this->gatewayFields as $gatewayField) {
            if (array_key_exists($gatewayField, $parameters)) {
                $this->gatewayData[$gatewayField] = $parameters[$gatewayField];
            }
        }

        return parent::initialize($parameters);
    }
}