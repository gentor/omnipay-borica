<?php


namespace Omnipay\Borica\Message;


class AuthorizeByTokenRequest extends PayByTokenRequest
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
}