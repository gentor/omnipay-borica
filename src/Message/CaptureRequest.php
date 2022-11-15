<?php


namespace Omnipay\Borica\Message;


class CaptureRequest extends AbstractRequest
{
    const TR_TYPE = 21;

    public function getData()
    {
        $data = parent::getData();
        unset($data['TRAN_TRTYPE']);

        $this->validate('order', 'amount', 'currency', 'description', 'RRN', 'INT_REF');

        $data = array_merge($data, [
            'TRTYPE' => self::TR_TYPE,
            'AMOUNT' => $this->getAmount(),
            'CURRENCY' => $this->getCurrency(),
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

    /**
     * @param mixed $data
     * @return FetchTransactionResponse
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

        $data = array_merge($data, json_decode($response->getBody()->getContents(), true));

        return $this->response = new FetchTransactionResponse($this, $data);
    }
}