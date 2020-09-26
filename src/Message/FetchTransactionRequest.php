<?php


namespace Omnipay\Borica\Message;


use Omnipay\Common\Message\ResponseInterface;

class FetchTransactionRequest extends AbstractRequest
{
    const TR_TYPE = 90;

    public function getData()
    {
        $data = parent::getData();

        $this->validate('order', 'transactionType');

        return array_merge($data, [
            'TRTYPE' => self::TR_TYPE,
            'NONCE' => $this->getNonce() ?: bin2hex(microtime(true)),
            'ORDER' => $this->getOrder(),
            'TRAN_TRTYPE' => $this->getTransactionType(),
        ]);
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

    /**
     * @param mixed $data
     * @return FetchTransactionResponse
     */
    public function sendData($data)
    {
        $data['P_SIGN'] = $this->sign($data);

        $response = $this->httpClient->request(
            'POST',
            $this->getEndpoint(),
            [
                'Content-type' => 'application/x-www-form-urlencoded',
            ],
            http_build_query($data)
        );

        $responseData = (array)json_decode($response->getBody()->getContents());

        $data = array_merge($responseData, [
            'TRTYPE' => $this->getTransactionType(),
            'ORDER' => $this->getOrder(),
            'DESC' => $this->getDescription(),
        ]);

        return $this->response = new FetchTransactionResponse($this, $data);
    }

    /**
     * Send the request
     *
     * @return FetchTransactionResponse|ResponseInterface
     */
    public function send()
    {
        return parent::send();
    }
}