<?php


namespace Omnipay\Borica\Message;


use GuzzleHttp\Client;

class PurchaseRequest extends AbstractRequest
{
    public function getData()
    {
        $data = parent::getData();

        $this->validate('amount', 'currency', 'order', 'description', 'merchantUrl', 'returnUrl');

        $data = array_merge($data, [
            'TRTYPE' => 1,
            'AMOUNT' => $this->getAmount(),
            'CURRENCY' => $this->getCurrency(),
            'MERCH_URL' => $this->getMerchantUrl(),
            'MERCH_NAME' => $this->getMerchantName() ?: $this->getMerchantUrl(),
            'BACKREF' => $this->getReturnUrl(),
            'NONCE' => $this->getNonce() ?: bin2hex(microtime(true)),
            'ORDER' => $this->getOrder(),
            'DESC' => $this->getDescription(),
            'AD.CUST_BOR_ORDER_ID' => $this->getOrder() . '|' . $this->getOrderId(),
            'ADDENDUM' => 'AD,TD',
        ]);

        $data['P_SIGN'] = $this->sign($data);

        return $data;
    }

    /**
     * Send the request
     *
     * @return PurchaseResponse|\Omnipay\Common\Message\ResponseInterface
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

    public function getOrderId()
    {
        return $this->getParameter('orderId');
    }

    public function setOrderId($value)
    {
        return $this->setParameter('orderId', $value);
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

    public function setMerchantUrl($value)
    {
        return $this->setParameter('merchantUrl', $value);
    }

    public function getMerchantName()
    {
        return $this->getParameter('merchantName');
    }

    public function setMerchantName($value)
    {
        return $this->setParameter('merchantName', $value);
    }

    /**
     * @param array $data
     * @return PurchaseResponse
     */
    public function sendData($data)
    {
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