<?php


namespace Omnipay\Borica\Message;


use Omnipay\Common\Exception\InvalidResponseException;

class RefundRequest extends FetchTransactionRequest
{
    const TR_TYPE = 24;

    public function getData()
    {
        $data = parent::getData();
        unset($data['TRAN_TRTYPE']);

        $this->validate('amount', 'currency', 'description', 'RRN', 'INT_REF');

        $data = array_merge($data, [
            'TRTYPE' => self::TR_TYPE,
            'AMOUNT' => $this->getAmount(),
            'ORG_AMOUNT' => $this->getAmount(),
            'CURRENCY' => $this->getCurrency(),
            'BACKREF' => $this->getReturnUrl(),
            'ORDER' => $this->getOrder(),
            'DESC' => $this->getDescription(),
            'RRN' => $this->getRRN(),
            'INT_REF' => $this->getIntRef(),
            'MERCHANT' => $this->getMerchant(),
        ]);

        $data['P_SIGN'] = $this->sign($data);

        return $data;
    }

    public function getIntRef()
    {
        return $this->getParameter('INT_REF');
    }

    public function setIntRef($value)
    {
        return $this->setParameter('INT_REF', $value);
    }

    public function getRRN()
    {
        return $this->getParameter('RRN');
    }

    public function setRRN($value)
    {
        return $this->setParameter('RRN', $value);
    }

    public function setTRTYPE($value)
    {
        return $this->setTransactionType($value);
    }

    /**
     * @param mixed $data
     * @return FetchTransactionResponse
     * @throws InvalidResponseException
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

        $html = $response->getBody()->getContents();
        preg_match_all('/input\s+type="hidden"\s+name="([^"]*)"\s+value="([^"]*)"/', $html, $matches);
        $result = array_combine($matches[1], $matches[2]);
        preg_match('/Error message:\s+([^\n]*)/', $html, $error);

        if (!empty($error[1])) {
            return $this->response = new FetchTransactionResponse($this, array_merge($result, [
                'RC' => $result['RC'],
                'STATUSMSG' => $error[1],
            ]));
        }

        $data = [
            'TERMINAL' => $this->getTerminalId(),
            'TRTYPE' => FetchTransactionRequest::TR_TYPE,
            'ORDER' => $this->getOrder(),
            'NONCE' => $data['NONCE'],
            'TRAN_TRTYPE' => self::TR_TYPE,
            'CARD' => $data['CARD'] ?? null,
        ];

        parent::setTransactionType(self::TR_TYPE);

        return parent::sendData($data);
    }
}