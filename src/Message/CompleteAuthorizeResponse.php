<?php

namespace Omnipay\Iyzico\Message;

use Omnipay\Iyzico\Constants\PaymentPhase;

/**
 * Result of finalizing a 3D Secure pre-authorization. Exposes the paymentId
 * (needed to capture/void the hold) and the fraud status, and confirms the
 * finalized payment is genuinely a hold (phase=PRE_AUTH), not a captured sale.
 */
class CompleteAuthorizeResponse extends CompletePurchaseResponse
{
    public function isSuccessful(): bool
    {
        return parent::isSuccessful() && ($this->getData()->phase ?? null) === PaymentPhase::PRE_AUTH;
    }

    public function getTransactionReference(): ?string
    {
        return isset($this->getData()->paymentId) ? (string) $this->getData()->paymentId : null;
    }

    public function getFraudStatus(): ?int
    {
        return $this->getData()->fraudStatus ?? null;
    }
}
