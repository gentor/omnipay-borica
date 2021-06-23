<?php


namespace Omnipay\Borica\Message;


use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\ResponseInterface;

class PayByTokenRequest extends PurchaseRequest
{
    protected $invalidResponseData = [];

    public function getData()
    {
        $data = parent::getData();

        $this->validate('MERCH_TOKEN_ID', 'MERCH_TRAN_STATE');

        unset($data['MK_TOKEN']);
        $data['MERCH_TOKEN_ID'] = $this->getParameter('MERCH_TOKEN_ID');

        return $data;
    }

    public function getInvalidResponseData()
    {
        return $this->invalidResponseData;
    }

    /**
     * Send the request
     *
     * @return FetchTransactionResponse|ResponseInterface
     * @throws InvalidRequestException
     * @throws InvalidResponseException
     */
    public function send()
    {
        return parent::send();
    }

    /**
     * @param mixed $data
     * @return CompletePurchaseResponse
     */
    public function sendData($data)
    {
        $response = $this->httpClient->request(
            'POST',
            $this->getEndpoint(),
            [
                'Content-type' => 'application/x-www-form-urlencoded',
            ],
            http_build_query($data)
        );

        $responseContents = $response->getBody()->getContents();

        preg_match_all('/input\s+type="hidden"\s+name="([^"]*)"\s+value="([^"]*)"/', $responseContents, $matches);
        $result = array_combine($matches[1], $matches[2]);
        preg_match('/Error message:\s+([^\n]*)/', $responseContents, $error);

        if (!empty($result) && !empty($error[1])) {
            $this->invalidResponseData = $result;
            throw new InvalidResponseException($error[1], $result['RC']);
        } elseif (empty($result)) {
            $this->invalidResponseData = ['contents' => $responseContents];
            throw new InvalidResponseException('Invalid gateway response');
        }

        return $this->response = new FetchTransactionResponse($this, $result);
    }
}