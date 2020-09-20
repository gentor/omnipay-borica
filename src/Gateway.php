<?php

namespace Omnipay\Borica;


use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Message\NotificationInterface;
use Omnipay\Common\Message\RequestInterface;

/**
 * @method NotificationInterface acceptNotification(array $options = array())
 * @method RequestInterface authorize(array $options = array())
 * @method RequestInterface completeAuthorize(array $options = array())
 * @method RequestInterface capture(array $options = array())
 * @method RequestInterface completePurchase(array $options = array())
 * @method RequestInterface refund(array $options = array())
 * @method RequestInterface fetchTransaction(array $options = [])
 * @method RequestInterface void(array $options = array())
 * @method RequestInterface createCard(array $options = array())
 * @method RequestInterface updateCard(array $options = array())
 * @method RequestInterface deleteCard(array $options = array())
 */
class Gateway extends AbstractGateway
{

    public function getName()
    {
        return 'Borica';
    }

    public function getDefaultParameters()
    {
        return [
            'terminalId' => '',
            'privateKey' => '',
            'certificate' => '',
            'currency' => 'BGN',
            'testMode' => true,
        ];
    }

    public function getTerminalId()
    {
        return $this->getParameter('terminalId');
    }

    public function setTerminalId($value)
    {
        return $this->setParameter('terminalId', $value);
    }

    public function getPrivateKey()
    {
        return $this->getParameter('privateKey');
    }

    public function setPrivateKey($value)
    {
        return $this->setParameter('privateKey', $value);
    }

    public function getCertificate()
    {
        return $this->getParameter('certificate');
    }

    public function setCertificate($value)
    {
        return $this->setParameter('certificate', $value);
    }

    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Borica\Message\PurchaseRequest', $parameters);
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement @method \Omnipay\Common\Message\NotificationInterface acceptNotification(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface authorize(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface completeAuthorize(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface capture(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface completePurchase(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface refund(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface fetchTransaction(array $options = [])
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface void(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface createCard(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface updateCard(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface deleteCard(array $options = array())
    }
}