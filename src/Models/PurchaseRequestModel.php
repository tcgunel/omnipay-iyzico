<?php

namespace Omnipay\Iyzico\Models;

class PurchaseRequestModel extends BaseModel
{
    public function __construct(?array $abstract)
    {
        parent::__construct($abstract);
    }

    public string $locale;
    public string $conversationId;
    public $price;
    public $paidPrice;
    public int $installment;
    public string $paymentChannel;
    public string $basketId;
    public string $paymentGroup;

    public PaymentCard $paymentCard;

    public PurchaserModel $buyer;

    public AddressModel $shippingAddress;

    public AddressModel $billingAddress;

    /**
     * @var ProductModel[]
     */
    public $basketItems;

    public string $paymentSource;

    public string $currency;
}
