<?php

namespace Omnipay\Iyzico\Tests\Feature;

use Omnipay\Iyzico\Helpers\Helper;
use Omnipay\Iyzico\Message\PreAuthEnrolmentRequest;
use Omnipay\Iyzico\Message\PreAuthRequest;
use Omnipay\Iyzico\Message\PreAuthResponse;
use Omnipay\Iyzico\Tests\TestCase;

class PreAuthTest extends TestCase
{
    /**
     * A non-3D pre-authorization is the same body as a normal auth, signed for
     * the /payment/preauth path.
     */
    public function test_preauth_request()
    {
        $options = json_decode(file_get_contents(__DIR__ . '/../Mock/ChargeRequest.json'), true, 512, JSON_THROW_ON_ERROR);

        $request = new PreAuthRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize($options);

        $data = $request->getData();

        self::assertSame('https://api.iyzipay.com/payment/preauth', $request->getEndpoint());
        self::assertStringStartsWith('IYZWSv2 ', $data['headers']->Authorization);

        // Independently recompute the signature over the exact request body with
        // the pre-auth URI path — proves the request signs /payment/preauth.
        $expected = 'IYZWSv2 ' . Helper::hashV2(
            'sandbox-public',
            'sandbox-private',
            (array) $data['request_params'],
            'TEST_RAND',
            '/payment/preauth'
        );

        self::assertSame($expected, $data['headers']->Authorization);
    }

    public function test_gateway_authorize_non_3d_routes_to_preauth()
    {
        $request = $this->gateway->authorize(['secure' => false]);

        self::assertInstanceOf(PreAuthRequest::class, $request);
    }

    public function test_gateway_authorize_3d_routes_to_preauth_enrolment()
    {
        $request = $this->gateway->authorize(['secure' => true]);

        self::assertInstanceOf(PreAuthEnrolmentRequest::class, $request);
        self::assertSame('https://api.iyzipay.com/payment/3dsecure/initialize/preauth', $request->getEndpoint());
    }

    public function test_preauth_response_success()
    {
        $httpResponse = $this->getMockHttpResponse('PreAuthResponseSuccess.txt');

        $response = new PreAuthResponse($this->getMockRequest(), $httpResponse);

        self::assertTrue($response->isSuccessful());
        self::assertSame('20000123', $response->getTransactionReference());
        self::assertSame(1, $response->getFraudStatus());
        self::assertSame('PRE_AUTH', $response->getData()->phase);
    }

    public function test_preauth_response_fraud_review_still_authorized()
    {
        $httpResponse = $this->getMockHttpResponse('PreAuthResponseFraudReview.txt');

        $response = new PreAuthResponse($this->getMockRequest(), $httpResponse);

        // Hold is placed (status success, phase PRE_AUTH) but fraud review pending.
        self::assertTrue($response->isSuccessful());
        self::assertSame(0, $response->getFraudStatus());
    }

    public function test_preauth_response_error()
    {
        $httpResponse = $this->getMockHttpResponse('PreAuthResponseError.txt');

        $response = new PreAuthResponse($this->getMockRequest(), $httpResponse);

        self::assertFalse($response->isSuccessful());
        self::assertSame('1000', $response->getCode());
        self::assertSame('Gecersiz imza', $response->getMessage());
        self::assertNull($response->getTransactionReference());
    }
}
