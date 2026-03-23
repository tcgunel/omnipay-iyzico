<?php

namespace Omnipay\Iyzico\Models;

class PaymentCard extends BaseModel
{
    public string $cardHolderName;
    public string $cardNumber;
    public string $expireYear;
    public string $expireMonth;
    public string $cvc;
    public int $registerCard;
}
