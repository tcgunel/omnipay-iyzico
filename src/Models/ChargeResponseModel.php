<?php

namespace Omnipay\Iyzico\Models;

class ChargeResponseModel extends BaseModel
{
    public string $status;

    public string $errorCode;
    public string $errorMessage;
    public string $errorGroup;

    public string $locale;
    public int $systemTime;
    public string $conversationId;

    public float $price;
    public float $paidPrice;
    public int $installment;

    public string $paymentId;

    /**
     * Ödeme işleminin fraud filtrelerine göre durumu.
     * Eğer ödemenin fraud risk skoru düşük ise ödemeye anında onay verilir, bu durumda 1 değeri döner.
     * Eğer fraud risk skoru yüksek ise ödeme işlemi reddedilir ve -1 döner.
     * Eğer ödeme işlemi daha sonradan incelenip karar verilecekse 0 döner.
     * Geçerli değerler: 0, -1 ve 1.
     * Üye işyeri sadece 1 olan işlemlerde ürünü kargoya vermelidir, 0 olan işlemler için bilgilendirme beklemelidir.
     *
     * @var int
     */
    public int $fraudStatus;

    public float $merchantCommissionRate;
    public float $merchantCommissionRateAmount;
    public float $iyziCommissionRateAmount;
    public float $iyziCommissionFee;

    public string $cardType;
    public string $cardAssociation;
    public string $cardFamily;
    public int $binNumber;
    public int $lastFourDigits;
    public string $basketId;
    public string $currency;

    /**
     * @var ItemTransactionModel[]
     */
    public $itemTransactions;

    public string $authCode;
    public string $phase;
    public string $hostReference;
}
