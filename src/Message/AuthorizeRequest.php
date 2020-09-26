<?php


namespace Omnipay\Borica\Message;


class AuthorizeRequest extends PurchaseRequest
{
    public function getData()
    {
        $data = parent::getData();

        $data = array_merge($data, [
            'TRTYPE' => 12,
        ]);

        $data['P_SIGN'] = $this->sign($data);

        return $data;
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