<?php


namespace Omnipay\Borica\Message;


use GuzzleHttp\Client;

class PurchaseRequest extends AbstractRequest
{
    public function getData()
    {
        $data = parent::getData();

        $data = array_merge($data, [
            'TRTYPE' => 1,
            'AMOUNT' => $this->getAmount(),
            'MERCH_URL' => $this->getMerchantUrl(),
            'BACKREF' => $this->getReturnUrl(),
            'NONCE' => $this->getNonce() ?: $data['NONCE'],
            'ORDER' => $this->getOrder(),
            'DESC' => $this->getDescription(),
        ]);

        $this->validate($data);

        return $data;
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

    public function getMerchantUrl()
    {
        return $this->getParameter('merchantUrl');
    }

    public function gstMerchantUrl($value)
    {
        return $this->setParameter('merchantUrl', $value);
    }

    /**
     * @param array $data
     * @return PurchaseResponse
     */
    public function sendData($data)
    {
        $data['P_SIGN'] = $this->sign($data);

//        $httpClient = new Client();
//
//        try {
//            $response = $httpClient->request('POST', $this->getEndpoint(), [
//                'form_params' => $data,
//            ]);
//        } catch (\Exception $e) {
//            dd($e);
//        }
//
//        $tmp = $response->getBody();

        return $this->response = new PurchaseResponse($this, $data);
    }
}