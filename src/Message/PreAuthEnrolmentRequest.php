<?php

namespace Omnipay\Iyzico\Message;

/**
 * iyzico 3D Secure pre-authorization initialization.
 *
 * Identical to the normal 3DS enrolment ({@see EnrolmentRequest}) — it returns
 * the same self-posting threeDSHtmlContent challenge page — except the endpoint
 * (and signed URI path) is the pre-auth variant. After the 3DS challenge, the
 * flow is finalized with completeAuthorize() (/payment/v2/3dsecure/auth).
 */
class PreAuthEnrolmentRequest extends EnrolmentRequest
{
    protected $endpoint = '/payment/3dsecure/initialize/preauth';
}
