<?php

namespace Omnipay\Iyzico\Message;

/**
 * iyzico non-3D pre-authorization (ön otorizasyon).
 *
 * The request body is identical to a normal auth (ChargeRequest); only the
 * endpoint — and therefore the signed URI path — changes to /payment/preauth.
 * The response comes back with phase=PRE_AUTH and the paymentId used to later
 * capture (postAuth) or void (cancel) the hold.
 */
class PreAuthRequest extends ChargeRequest
{
    protected $endpoint = '/payment/preauth';

    /**
     * @throws \Omnipay\Iyzico\Exceptions\OmnipayIyzicoHashValidationException
     */
    protected function createResponse($data): PreAuthResponse
    {
        return $this->response = new PreAuthResponse($this, $data);
    }
}
