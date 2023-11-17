<?php

namespace Omnipay\Borica;


use Omnipay\Borica\Message\AuthorizeByTokenRequest;
use Omnipay\Borica\Message\PayByTokenRequest;
use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Common\Message\NotificationInterface;
use Omnipay\Common\Message\RequestInterface;

/**
 * @method NotificationInterface acceptNotification(array $options = array())
 * @method RequestInterface void(array $options = array())
 * @method RequestInterface createCard(array $options = array())
 * @method RequestInterface updateCard(array $options = array())
 * @method RequestInterface deleteCard(array $options = array())
 *
 * @see https://3dsgate-dev.borica.bg/P-OM-41_BORICA_eCommerce_CGI_interface_v3.0_EN.pdf
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
            'signScheme' => 'MAC_ADVANCED',
            'currency' => 'BGN',
            'testMode' => true,
            'validateStatusData' => true,
            'gatewayCertificate' => file_get_contents(__DIR__ . '/../resources/MPI_OW_APGW_B-Trust.cer'),
        ];
    }

    /**
     * @param boolean $value
     * @return $this
     */
    public function setTestMode($value)
    {
        if ($value) {
            $this->setGatewayCertificate(file_get_contents(__DIR__ . '/../resources/MPI_OW_APGW_B-Trust.cer'));
        } else {
            $this->setGatewayCertificate(file_get_contents(__DIR__ . '/../resources/MPI_OW_APGW.cer'));
        }

        return $this->setParameter('testMode', $value);
    }

    public function useMacGeneral()
    {
        return $this->setParameter('signScheme', 'MAC_GENERAL');
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

    public function getValidateStatusData()
    {
        return $this->getParameter('validateStatusData');
    }

    public function setValidateStatusData(bool $value)
    {
        return $this->setParameter('validateStatusData', $value);
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
     * @return Message\CompleteAuthorizeRequest|AbstractRequest
     */
    public function completeAuthorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Borica\Message\CompleteAuthorizeRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return Message\CaptureRequest|AbstractRequest
     */
    public function capture(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Borica\Message\CaptureRequest', $parameters);
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

    /**
     * @param array $parameters
     * @return Message\CancelRequest|AbstractRequest
     */
    public function cancel(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Borica\Message\CancelRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return PayByTokenRequest
     */
    public function payByToken(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Borica\Message\PayByTokenRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return AuthorizeByTokenRequest
     */
    public function authorizeByToken(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Borica\Message\AuthorizeByTokenRequest', $parameters);
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement @method \Omnipay\Common\Message\NotificationInterface acceptNotification(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface void(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface createCard(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface updateCard(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface deleteCard(array $options = array())
    }
}