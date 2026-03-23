<?php

namespace Omnipay\Iyzico\Models;

class PayWithIyzicoResponseModel extends BaseModel
{
    public string $status;
    public string $locale;
    public int $systemTime;
    public string $conversationId;
    public string $token;
    public string $checkoutFormContent;
    public int $tokenExpireTime;
    public string $paymentPageUrl;
    public string $payWithIyzicoPageUrl;

    public string $errorCode;
    public string $errorMessage;
    public string $errorGroup;
}
