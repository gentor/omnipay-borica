<?php

namespace Omnipay\Borica;


use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Common\Message\NotificationInterface;
use Omnipay\Common\Message\RequestInterface;

/**
 * @method NotificationInterface acceptNotification(array $options = array())
 * @method RequestInterface completeAuthorize(array $options = array())
 * @method RequestInterface capture(array $options = array())
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
            'currency' => 'BGN',
            'testMode' => true,
            'gatewayCertificate' => file_get_contents(__DIR__ . '/../resources/MPI_OW_APGW_B-Trust.cer'),
            'signScheme' => 'MAC_COMMON',
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

    public function getGatewayCertificate()
    {
        return $this->getParameter('gatewayCertificate');
    }

    public function setGatewayCertificate($value)
    {
        return $this->setParameter('gatewayCertificate', $value);
    }

    public function getSignScheme()
    {
        return $this->getParameter('signScheme');
    }

    public function setSignScheme($value)
    {
        return $this->setParameter('signScheme', $value);
    }

    /**
     * @param array $parameters
     * @return Message\AuthorizeRequest|AbstractRequest
     */
    public function authorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Borica\Message\AuthorizeRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return Message\PurchaseRequest|AbstractRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Borica\Message\PurchaseRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return Message\CompletePurchaseRequest|AbstractRequest
     */
    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Borica\Message\CompletePurchaseRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return Message\FetchTransactionRequest|AbstractRequest
     */
    public function fetchTransaction(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Borica\Message\FetchTransactionRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return Message\RefundRequest|AbstractRequest
     */
    public function refund(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Borica\Message\RefundRequest', $parameters);
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement @method \Omnipay\Common\Message\NotificationInterface acceptNotification(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface completeAuthorize(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface capture(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface void(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface createCard(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface updateCard(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface deleteCard(array $options = array())
    }
}