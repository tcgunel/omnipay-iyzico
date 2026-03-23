<?php

namespace Omnipay\Iyzico\Models;

class RequestHeadersModel extends BaseModel
{
    public function __toArray()
    {

        return [
            'Authorization' => $this->Authorization,
            'x-iyzi-rnd' => $this->x_iyzi_rnd,
            'x-iyzi-client-version' => $this->x_iyzi_client_version,
        ];
    }

    public $Authorization;

    protected $x_iyzi_rnd;

    protected $x_iyzi_client_version;
}
