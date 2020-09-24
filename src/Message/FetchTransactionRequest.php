<?php


namespace Omnipay\Borica\Message;


use Omnipay\Borica\Signature;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Exception\InvalidResponseException;

class FetchTransactionRequest extends AbstractRequest
{
    public function getData()
    {
        $data = parent::getData();

        $this->validate('order', 'transactionType');

        return array_merge($data, [
            'TRTYPE' => 90,
            'NONCE' => $this->getNonce() ?: bin2hex(microtime(true)),
            'ORDER' => $this->getOrder(),
            'TRAN_TRTYPE' => $this->getTransactionType(),
        ]);
    }

    /**
     * Send the request
     *
     * @return FetchTransactionResponse|\Omnipay\Common\Message\ResponseInterface
     */
    public function send()
    {
        return parent::send();
    }

    public function getOrder()
    {
        return $this->getParameter('order');
    }

    public function setOrder($value)
    {
        return $this->setParameter('order', $value);
    }

    public function getNonce()
    {
        return $this->getParameter('nonce');
    }

    public function setNonce($value)
    {
        return $this->setParameter('nonce', $value);
    }

    public function getTransactionType()
    {
        return $this->getParameter('transactionType');
    }

    public function setTransactionType($value)
    {
        return $this->setParameter('transactionType', $value);
    }

    public function validateGatewaySignature(array $data)
    {
        if (!$this->getGatewayCertificate()) {
            return;
        }

        $valid = Signature::verify($data, $this->getGatewayCertificate());
        if ($valid < 1) {
            throw new InvalidResponseException("Invalid gateway signature (P_SIGN): " . $data['P_SIGN']);
        }
    }

    /**
     * @param array $data
     * @return FetchTransactionResponse
     */
    public function sendData($data)
    {
        $data['P_SIGN'] = $this->sign($data);

        $response = $this->httpClient->request('GET', $this->getEndpoint() . '?' . http_build_query($data));
        $responseData = (array)json_decode($response->getBody()->getContents());

        $this->validateGatewaySignature([
            'TRTYPE' => 90,
            'TERMINAL' => $responseData['terminal'] ?? null,
            'AMOUNT' => $responseData['amount'] ?? null,
            'TIMESTAMP' => $responseData['timestamp'] ?? null,
            'P_SIGN' => $responseData['signature'] ?? null,
        ]);

        $data = array_merge($responseData, [
            'TRTYPE' => $this->getTransactionType(),
            'ORDER' => $this->getOrder(),
        ]);

        return $this->response = new FetchTransactionResponse($this, $data);
    }
}