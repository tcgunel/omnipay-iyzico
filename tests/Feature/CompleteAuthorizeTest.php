<?php

namespace Omnipay\Iyzico\Tests\Feature;

use Omnipay\Iyzico\Helpers\Helper;
use Omnipay\Iyzico\Message\CompleteAuthorizeRequest;
use Omnipay\Iyzico\Message\CompleteAuthorizeResponse;
use Omnipay\Iyzico\Tests\TestCase;

class CompleteAuthorizeTest extends TestCase
{
    private function baseOptions(): array
    {
        return [
            'publicKey' => 'sandbox-public',
            'privateKey' => 'sandbox-private',
            'randomString' => 'TEST_RAND',
            'testMode' => false,
            'language' => 'tr',
            'transactionId' => '123456789',
            'paymentId' => '20000123',
            'amount' => '150.00',
            'currency' => 'TRY',
        ];
    }

    public function test_gateway_complete_authorize_routes()
    {
        self::assertInstanceOf(CompleteAuthorizeRequest::class, $this->gateway->completeAuthorize());
    }

    /**
     * The 3DS pre-auth finalize signs the shared v2 3DS-auth endpoint.
     */
    public function test_complete_authorize_request()
    {
        $request = new CompleteAuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize($this->baseOptions());

        $data = $request->getData();

        self::assertSame('https://api.iyzipay.com/payment/v2/3dsecure/auth', $request->getEndpoint());

        $expected = 'IYZWSv2 ' . Helper::hashV2(
            'sandbox-public',
            'sandbox-private',
            (array) $data['request_params'],
            'TEST_RAND',
            '/payment/v2/3dsecure/auth'
        );

        self::assertSame($expected, $data['headers']->Authorization);
    }

    public function test_complete_authorize_response_success()
    {
        $httpResponse = $this->getMockHttpResponse('CompleteAuthorizeResponseSuccess.txt');

        $response = new CompleteAuthorizeResponse($this->getMockRequest(), $httpResponse);

        self::assertTrue($response->isSuccessful());
        self::assertSame('20000123', $response->getTransactionReference());
        self::assertSame(1, $response->getFraudStatus());
    }

    /**
     * A finalize that came back as a captured sale (phase=AUTH) must NOT be
     * accepted as a successful pre-authorization — this is the whole reason the
     * pre-auth finalize is a distinct response class.
     */
    public function test_complete_authorize_rejects_non_preauth_phase()
    {
        $httpResponse = $this->getMockHttpResponse('CompleteAuthorizeResponseWrongPhase.txt');

        $response = new CompleteAuthorizeResponse($this->getMockRequest(), $httpResponse);

        self::assertFalse($response->isSuccessful());
    }

    public function test_complete_authorize_response_error()
    {
        $httpResponse = $this->getMockHttpResponse('CompleteAuthorizeResponseError.txt');

        $response = new CompleteAuthorizeResponse($this->getMockRequest(), $httpResponse);

        self::assertFalse($response->isSuccessful());
        self::assertSame('5088', $response->getCode());
        self::assertSame('3D dogrulamasi basarisiz', $response->getMessage());
    }

    /**
     * Regression: getMessage()/getCode() on a SUCCESS response (which carries no
     * errorMessage/errorCode) must return null, not throw a fatal Error.
     */
    public function test_success_response_message_accessors_do_not_throw()
    {
        $httpResponse = $this->getMockHttpResponse('CompleteAuthorizeResponseSuccess.txt');

        $response = new CompleteAuthorizeResponse($this->getMockRequest(), $httpResponse);

        self::assertNull($response->getMessage());
        self::assertNull($response->getCode());
    }
}
