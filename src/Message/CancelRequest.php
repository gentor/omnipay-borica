<?php


namespace Omnipay\Borica\Message;


class CancelRequest extends CaptureRequest
{
    const TR_TYPE = 22;

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
}