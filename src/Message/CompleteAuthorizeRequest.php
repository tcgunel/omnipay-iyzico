<?php

namespace Omnipay\Iyzico\Message;

/**
 * Finalize a 3D Secure pre-authorization after the challenge returns.
 *
 * iyzico uses the same v2 3DS-auth endpoint to finalize both a normal 3DS sale
 * and a 3DS pre-authorization ({@see CompletePurchaseRequest}); the resulting
 * phase (AUTH vs PRE_AUTH) tells them apart. This subclass only swaps in a
 * response that surfaces the paymentId + fraudStatus and asserts PRE_AUTH.
 */
class CompleteAuthorizeRequest extends CompletePurchaseRequest
{
    /**
     * @throws \Omnipay\Iyzico\Exceptions\OmnipayIyzicoHashValidationException
     * @throws \JsonException
     */
    protected function createResponse($data): CompleteAuthorizeResponse
    {
        return $this->response = new CompleteAuthorizeResponse($this, $data);
    }
}
