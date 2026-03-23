<?php

namespace Omnipay\Iyzico\Models;

/**
 * Parameter descriptions directly taken from official documentation.
 *
 * Kayıtsız kart ile ödeme isteğinde cardOwnerName, cardNumber, cardExpireMonth, cardExpireYear, cardCvc alanları zorunludur.
 * userId ve cardId bilgileri gönderilmemelidir ve token hesaplaması yapılırken userId ve cardId bilgileri String “” olarak
 * hesaplamaya eklenmelidir.
 *
 * Kayıtlı kart ile ödeme isteğinde userId ve cardId bilgileri zorunludur.
 * cardOwnerName, cardNumber, cardExpireMonth, cardExpireYear alanları gönderilmemelidir ve token hesaplaması yapılırken
 * String “” olarak hesaplamaya eklenmelidir.
 * cardCvc alanı kayıtlı kart ile ödeme talebinde opsiyoneldir, gönderilir ise token hesaplamasına dahil edilmelidir.
 *
 * @link https://dev.ipara.com.tr/home/PaymentServices#apiPayment
 */
class ChargeRequestModel extends PurchaseRequestModel
{
    public function __construct(?array $abstract)
    {
        parent::__construct($abstract);
    }

    /**
     * 3D Secure olmadan gerçekleştirilen ödeme işlemlerinde “false” olarak gönderilmelidir.
     *
     * @required
     * @var boolean
     */
    //    public $threeD = false;

    /**
     * 3D Secure onay kodu.
     *
     * @required
     * @var string
     */
    //    public $threeDSecureCode = "";
}
