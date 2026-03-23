<?php

namespace Omnipay\Iyzico\Models;

class ConvertedPayoutModel extends BaseModel
{
    public float $paidPrice;
    public float $iyziCommissionRateAmount;
    public float $iyziCommissionFee;
    public float $blockageRateAmountMerchant;
    public float $blockageRateAmountSubMerchant;
    public float $subMerchantPayoutAmount;
    public float $merchantPayoutAmount;
    public float $iyziConversionRate;
    public float $iyziConversionRateAmount;
    public string $currency;

}
