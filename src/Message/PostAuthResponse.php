<?php

namespace Omnipay\Iyzico\Message;

use Omnipay\Iyzico\Constants\PaymentPhase;

/**
 * Response of an iyzico capture (postAuth). Reuses the charge response shape
 * (status / errorCode / errorMessage / paymentId / phase=POST_AUTH).
 */
class PostAuthResponse extends ChargeResponse
{
    /**
     * A capture is successful only when iyzico confirms status=success AND the
     * payment moved to phase=POST_AUTH — a success response in any other phase
     * must not be reported as a completed capture (money-safety).
     */
    public function isSuccessful(): bool
    {
        return parent::isSuccessful() && ($this->getData()->phase ?? null) === PaymentPhase::POST_AUTH;
    }

    /**
     * The iyzico paymentId of the now-captured payment.
     */
    public function getTransactionReference(): ?string
    {
        return isset($this->getData()->paymentId) ? (string) $this->getData()->paymentId : null;
    }
}
