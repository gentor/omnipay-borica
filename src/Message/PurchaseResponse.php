<?php


namespace Omnipay\Borica\Message;


use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;

class PurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{
    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isRedirect()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->request->getEndpoint();
    }

    /**
     * @return string
     */
    public function getRedirectMethod()
    {
        return 'POST';
    }

    public function getRedirectData()
    {
        return $this->data;
    }

    public function getTransactionId()
    {
        return $this->data['NONCE'];
    }
}