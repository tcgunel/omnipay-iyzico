<?php

namespace Omnipay\Iyzico\Message;

use Omnipay\Iyzico\Constants\PaymentPhase;

/**
 * Response of an iyzico non-3D pre-authorization.
 *
 * A successful pre-auth returns status=success AND phase=PRE_AUTH — a payment
 * that came back captured (phase=AUTH) instead of authorized would be a
 * money-handling bug, so isSuccessful() asserts the phase explicitly.
 */
class PreAuthResponse extends ChargeResponse
{
    public function isSuccessful(): bool
    {
        return parent::isSuccessful() && ($this->getData()->phase ?? null) === PaymentPhase::PRE_AUTH;
    }

    /**
     * The iyzico paymentId — the key needed to later capture (postAuth) or void.
     */
    public function getTransactionReference(): ?string
    {
        return isset($this->getData()->paymentId) ? (string) $this->getData()->paymentId : null;
    }

    /**
     * Fraud filter status: 1 = approved, 0 = under review (wait), -1 = rejected.
     * Merchants should only fulfil on 1; 0 means await iyzico's decision.
     */
    public function getFraudStatus(): ?int
    {
        return $this->getData()->fraudStatus ?? null;
    }
}
