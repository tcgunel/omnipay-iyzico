<?php

namespace Omnipay\Iyzico\Models;

class CancelRequestModel extends BaseModel
{
    public $locale;

    public $conversationId;

    public $paymentId;

    public $ip;

    public $reason;

    public $description;
}
