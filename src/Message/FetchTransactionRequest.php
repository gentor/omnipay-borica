<?php


namespace Omnipay\Borica\Message;


use Omnipay\Common\Exception\InvalidRequestException;
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
     * @throws InvalidRequestException
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

        $responseContents = $response->getBody()->getContents();
        $responseData = json_decode($responseContents, true);

        if (is_null($responseData)) {
            preg_match_all('/input\s+type="hidden"\s+name="([^"]*)"\s+value="([^"]*)"/', $responseContents, $matches);
            $result = array_combine($matches[1], $matches[2]);
            preg_match('/Error message:\s+([^\n]*)/', $responseContents, $error);

            if (!empty($error[1])) {
                $signature = $result['P_SIGN'] ?? null;
                unset($result['P_SIGN']);

                return $this->response = new FetchTransactionResponse($this, array_merge($result, [
                    'TERMINAL' => $result['TERMINAL'] ?? $this->getTerminalId(),
                    'TIMESTAMP' => $result['TIMESTAMP'] ?? $data['TIMESTAMP'],
                    'TRTYPE' => $this->getTransactionType(),
                    'AMOUNT' => $this->getAmount(),
                    'CURRENCY' => $this->getCurrency(),
                    'NONCE' => $this->getNonce(),
                    'ORDER' => $this->getOrder(),
                    'DESC' => $this->getDescription(),
                    'responseCode' => $result['RC'] ?? null,
                    'statusMsg' => $error[1],
                    'signature' => $signature,
                ]));
            }
        }

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