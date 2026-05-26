<?php

namespace Omnipay\Iyzico\Models;

class CompletePurchaseRequestModel extends BaseModel
{
    public string $locale;
    public string $conversationId;
    public string $paymentId;
    public string $paidPrice;
    public string $basketId;
    public string $currency;
}
