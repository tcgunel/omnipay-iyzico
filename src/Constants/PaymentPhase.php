<?php

namespace Omnipay\Iyzico\Constants;

/**
 * iyzico payment "phase" returned on payment/detail and auth responses.
 *
 * AUTH      => a normal immediate sale.
 * PRE_AUTH  => a pre-authorization (ön otorizasyon): funds held, not captured.
 * POST_AUTH => a captured pre-authorization (ön otorizasyon kapama).
 */
class PaymentPhase
{
    public const AUTH = 'AUTH';

    public const PRE_AUTH = 'PRE_AUTH';

    public const POST_AUTH = 'POST_AUTH';
}
