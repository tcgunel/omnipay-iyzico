<?php

namespace Omnipay\Iyzico\Models;

/**
 * Capture (ön otorizasyon kapama / postAuth) request body.
 *
 * paidPrice is trimmed to iyzico's decimal format ("10.5", not "10.50") by
 * {@see BaseModel::formatFields()} via format_paidPrice, so the signed and sent
 * bodies stay byte-identical. paidPrice may be LESS than the authorized amount
 * for a partial capture (iyzico enforces the allowed range).
 */
class PostAuthRequestModel extends BaseModel
{
    public string $locale;

    public string $conversationId;

    public string $paymentId;

    /**
     * @var float|string
     */
    public $paidPrice;

    public string $ip;

    public string $currency;
}
