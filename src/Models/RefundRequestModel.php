<?php

namespace Omnipay\Iyzico\Models;

class RefundRequestModel extends BaseModel
{
    public $locale;

    public $conversationId;

    public $paymentId;

    public $price;

    public $currency;

    public $ip;
}
