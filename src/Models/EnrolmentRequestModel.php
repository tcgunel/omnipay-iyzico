<?php

namespace Omnipay\Iyzico\Models;

class EnrolmentRequestModel extends PurchaseRequestModel
{
    public function __construct(?array $abstract)
    {
        parent::__construct($abstract);
    }

    public string $callbackUrl = '';
}
