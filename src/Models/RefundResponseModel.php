<?php

namespace Omnipay\Iyzico\Models;

use Omnipay\Iyzico\Traits\HasResponse;

class RefundResponseModel extends BaseModel
{
    use HasResponse;

    public $paymentId;

    public $price;

    public $currency;

    public $authCode;

    public $hostReference;

    public $refundHostReference;

    public $retryable;

    public $signature;
}
