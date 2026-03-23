<?php

namespace Omnipay\Iyzico\Models;

class ItemTransactionModel extends BaseModel
{
    public string $itemId;
    public int $paymentTransactionId;
    public int $transactionStatus;
    public float $price;
    public float $paidPrice;
    public float $merchantCommissionRate;
    public float $merchantCommissionRateAmount;
    public float $iyziCommissionRateAmount;
    public float $iyziCommissionFee;
    public float $blockageRate;
    public float $blockageRateAmountMerchant;
    public float $blockageRateAmountSubMerchant;
    public string $blockageResolvedDate;
    public float $subMerchantPrice;
    public float $subMerchantPayoutRate;
    public float $subMerchantPayoutAmount;
    public float $merchantPayoutAmount;

    /**
     * @var ConvertedPayoutModel
     */
    public $convertedPayout;
}
