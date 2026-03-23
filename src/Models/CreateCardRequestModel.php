<?php

namespace Omnipay\Iyzico\Models;

/**
 * Kullanıcının bir kartı kaydetmek istediği zaman kullanabileceği servistir.
 * Bir kayıtlı kart oluşturulmak istendiğinde kart oluşturma verilerini JSON formatında hazırlayarak,
 * https://api.ipara.com/bankcard/create adresine gerekli güvenlik bilgilerini http header bilgisine ekleyerek post eder.
 * iPara işlem sonucunu yine JSON formatında mağazaya döner.
 *
 * @link https://dev.ipara.com.tr/Home/WalletServices#generalwallet
 * @link https://dev.ipara.com.tr/Home/WalletServices#addcardtowallet
 */
class CreateCardRequestModel extends BaseModel
{
    /**
     * Mağaza kullanıcısını referans eden bilgi.
     *
     * @required
     * @var
     */
    public $userId;

    /**
     * Mağaza kullanıcısını referans eden bilgi.
     *
     * @required
     * @var
     */
    public $cardOwnerName;

    /**
     * Mağaza kullanıcısını referans eden bilgi.
     *
     * @required
     * @var
     */
    public $cardNumber;

    /**
     * Kart rumuz bilgisi.
     *
     * @required
     * @var
     */
    public $cardAlias;

    /**
     * Kart son kullanma tarihi ay parametresi. Uzunluk: 2 karakter. Örnek; 05,11, vb.
     *
     * @required
     * @var
     */
    public $cardExpireMonth;

    /**
     * Kart son kullanma tarihi yıl parametresi. Uzunluk: 2 karakter. Örnek; 14,19, vb.
     *
     * @required
     * @var
     */
    public $cardExpireYear;

    /**
     * Müşteri istemci IP adresi.
     *
     * @required
     * @var
     */
    public $clientIp = '127.0.0.1';
}
