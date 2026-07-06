<?php

namespace Omnipay\Iyzico;

use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Iyzico\Message\CancelRequest;
use Omnipay\Iyzico\Message\ChargeRequest;
use Omnipay\Iyzico\Message\CheckoutFormInquiryRequest;
use Omnipay\Iyzico\Message\CheckoutFormRequest;
use Omnipay\Iyzico\Message\CompleteAuthorizeRequest;
use Omnipay\Iyzico\Message\CompletePurchaseRequest;
use Omnipay\Iyzico\Message\EnrolmentRequest;
use Omnipay\Iyzico\Message\PaymentInquiryRequest;
use Omnipay\Iyzico\Message\PayWithIyzicoRequest;
use Omnipay\Iyzico\Message\PostAuthRequest;
use Omnipay\Iyzico\Message\PreAuthEnrolmentRequest;
use Omnipay\Iyzico\Message\PreAuthRequest;
use Omnipay\Iyzico\Message\RefundRequest;
use Omnipay\Iyzico\Message\VerifyEnrolmentRequest;
use Omnipay\Iyzico\Traits\PurchaseGettersSetters;

/**
 * Iyzico Gateway
 * (c) Tolga Can Günel
 * 2015, mobius.studio
 * http://www.github.com/tcgunel/omnipay-iyzico
 * @method \Omnipay\Common\Message\NotificationInterface acceptNotification(array $options = [])
 */
class Gateway extends AbstractGateway
{
    use PurchaseGettersSetters;

    public function getName(): string
    {
        return 'Iyzico';
    }

    public function getDefaultParameters()
    {
        return [
            'clientIp' => '127.0.0.1',

            'installment' => '1',
            'secure' => false,
            'publicKey' => '',
            'privateKey' => '',
            'language' => ['tr', 'en'],
            'randomString' => str_replace('.', '', uniqid('', true)),

            'isCheckoutInIframe' => true,

            'paymentChannel' => 'WEB',
            'paymentGroup' => 'PHYSICAL',

            'registrationDate' => date('Y-m-d H:i:s'),
            'lastLoginDate' => date('Y-m-d H:i:s'),

        ];
    }

    public function binLookup(array $parameters = []): AbstractRequest
    {
        return $this->createRequest('\Omnipay\Iyzico\Message\BinLookupRequest', $parameters);
    }

    public function purchase(array $parameters = [])
    {
        if (
            (array_key_exists('secure', $parameters) && $parameters['secure'] === true) ||
            $this->getSecure() === true
        ) {

            return $this->createRequest(EnrolmentRequest::class, $parameters);

        }

        return $this->createRequest(ChargeRequest::class, $parameters);
    }

    public function verifyEnrolment(array $parameters = []): AbstractRequest
    {
        return $this->createRequest(VerifyEnrolmentRequest::class, $parameters);
    }

    public function completePurchase(array $parameters = []): AbstractRequest
    {
        return $this->createRequest(CompletePurchaseRequest::class, $parameters);
    }

    public function paymentInquiry(array $parameters = []): AbstractRequest
    {
        return $this->createRequest(PaymentInquiryRequest::class, $parameters);
    }

    public function checkoutForm(array $parameters = []): AbstractRequest|CheckoutFormRequest
    {
        return $this->createRequest(CheckoutFormRequest::class, $parameters);
    }

    public function checkoutFormInquiry(array $parameters = []): AbstractRequest|CheckoutFormRequest
    {
        return $this->createRequest(CheckoutFormInquiryRequest::class, $parameters);
    }

    public function payWithIyzico(array $parameters = []): AbstractRequest|CheckoutFormRequest
    {
        return $this->createRequest(PayWithIyzicoRequest::class, $parameters);
    }

    public function refund(array $parameters = []): AbstractRequest
    {
        return $this->createRequest(RefundRequest::class, $parameters);
    }

    public function cancel(array $parameters = []): AbstractRequest
    {
        return $this->createRequest(CancelRequest::class, $parameters);
    }

    /**
     * Pre-authorization (ön otorizasyon): place a hold without capturing.
     * secure=true routes through the 3DS pre-auth initialization; otherwise a
     * direct /payment/preauth call.
     */
    public function authorize(array $parameters = []): AbstractRequest
    {
        if (
            (array_key_exists('secure', $parameters) && $parameters['secure'] === true) ||
            $this->getSecure() === true
        ) {

            return $this->createRequest(PreAuthEnrolmentRequest::class, $parameters);

        }

        return $this->createRequest(PreAuthRequest::class, $parameters);
    }

    /**
     * Finalize a 3D Secure pre-authorization after the challenge returns.
     */
    public function completeAuthorize(array $parameters = []): AbstractRequest
    {
        return $this->createRequest(CompleteAuthorizeRequest::class, $parameters);
    }

    /**
     * Capture (ön otorizasyon kapama / postAuth) a previously placed hold.
     */
    public function capture(array $parameters = []): AbstractRequest
    {
        return $this->createRequest(PostAuthRequest::class, $parameters);
    }

    /**
     * Void a pre-authorization hold (Omnipay-standard alias of cancel()).
     */
    public function void(array $parameters = []): AbstractRequest
    {
        return $this->createRequest(CancelRequest::class, $parameters);
    }
}
