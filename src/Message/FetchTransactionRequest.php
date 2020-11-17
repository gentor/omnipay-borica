<?php


namespace Omnipay\Borica\Message;


use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;

class FetchTransactionRequest extends AbstractRequest
{
    const TR_TYPE = 90;
    const GUARD_TIME = 900;
    const GUARD_TIME_CHECK_CODE = -40;

    protected $invalidResponseData = [];

    public function getData()
    {
        $data = parent::getData();

        $this->validate('order', 'transactionType');

        return array_merge($data, [
            'TRTYPE' => self::TR_TYPE,
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

    public function getTransactionType()
    {
        return $this->getParameter('transactionType');
    }

    public function setTransactionType($value)
    {
        return $this->setParameter('transactionType', $value);
    }

    public function getInvalidResponseData()
    {
        return $this->invalidResponseData;
    }

    /**
     * @param mixed $data
     * @return FetchTransactionResponse
     * @throws InvalidResponseException
     */
    public function sendData($data)
    {
        $responseCodeField = 'RC';
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
        if (($responseData[$responseCodeField] ?? '') == -24) {
            $this->invalidResponseData = $responseData;
            throw new InvalidResponseException('Gateway cache expired', -24);
        }

        if (is_null($responseData)) {
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
        }

        if ($responseData[$responseCodeField] == self::GUARD_TIME_CHECK_CODE) {
            try {
                $originalTimestamp = Uuid::fromString($responseData['NONCE'])->getDateTime()->getTimestamp();
            } catch (\Exception $e) {
                $originalTimestamp = time();
            }
            
            if ((time() - $originalTimestamp) > self::GUARD_TIME) {
//                throw new InvalidResponseException('Gateway guard time expired', self::GUARD_TIME_CHECK_CODE);
                $responseData[$responseCodeField] = -17;
                $responseData['STATUSMSG'] = 'Gateway guard time expired (' . self::GUARD_TIME . ' sec)';
            }
        }

        $data = array_merge($responseData, [
            'TRTYPE' => $this->getTransactionType(),
            'ORDER' => $this->getOrder(),
            'DESC' => $this->getDescription(),
            'MERCHANT' => $this->getMerchant(),
            'CARD' => $responseData['CARD'] ?? $this->getCard(),
        ]);

        return $this->response = new FetchTransactionResponse($this, $data);
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
}