<?php


namespace Omnipay\Borica\Message;


use Omnipay\Common\Message\ResponseInterface;

class PurchaseRequest extends AbstractRequest
{
    public function getData()
    {
        $data = parent::getData();

        $this->validate('amount', 'currency', 'order', 'description', 'merchant', 'merchantUrl');

        $data = array_merge($data, [
            'TRTYPE' => 1,
            'AMOUNT' => $this->getAmount(),
            'CURRENCY' => $this->getCurrency(),
            'MERCH_URL' => $this->getMerchantUrl(),
            'MERCH_NAME' => $this->getMerchantName() ?: $this->getMerchantUrl(),
            'MERCHANT' => $this->getMerchant() ?: $this->getMerchantUrl(),
            'BACKREF' => $this->getReturnUrl(),
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
     * @return PurchaseResponse|ResponseInterface
     */
    public function send()
    {
        return parent::send();
    }

    public function getOrderId()
    {
        return $this->getParameter('orderId');
    }

    public function setOrderId($value)
    {
        return $this->setParameter('orderId', $value);
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
     * @param mixed $data
     * @return PurchaseResponse
     */
    public function sendData($data)
    {
        return $this->response = new PurchaseResponse($this, $data);
    }
}