<?php

namespace Omnipay\Iyzico\Models;

/**
 * @link https://dev.iyzipay.com/tr/api/taksit-sorgulama
 */
class BinLookupRequestModel extends BaseModel
{
    /**
     * iyzico istek sonucunda dönen metinlerin dilini ayarlamak için kullanılır. Varsayılan değeri tr’dir.
     *
     * @var string|null
     */
    public $locale;

    /**
     * İstek esnasında gönderip, sonuçta alabileceğiniz bir değer, request/response eşleşmesi yapmak için kullanılabilir.
     *
     * @var string|null
     */
    public $conversationId;

    /**
     * Kredi veya Debit Kart numarasının ilk 6 hanesi. Örnek: 428220
     *
     * @var string|null
     */
    public $binNumber;

    /**
     * Taksitlendirilmek istenen tutar bilgisi.
     *
     * @var string|float|null
     */
    public $price;
}
