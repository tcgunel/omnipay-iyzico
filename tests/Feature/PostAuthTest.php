<?php

namespace Omnipay\Iyzico\Tests\Feature;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Iyzico\Helpers\Helper;
use Omnipay\Iyzico\Message\CancelRequest;
use Omnipay\Iyzico\Message\PostAuthRequest;
use Omnipay\Iyzico\Message\PostAuthResponse;
use Omnipay\Iyzico\Tests\TestCase;

class PostAuthTest extends TestCase
{
    private function baseOptions(): array
    {
        return [
            'publicKey' => 'sandbox-public',
            'privateKey' => 'sandbox-private',
            'randomString' => 'TEST_RAND',
            'testMode' => false,
            'language' => 'tr',
            'conversationId' => 'test-conv-id',
            'paymentId' => '20000123',
            'amount' => '15.75',
            'currency' => 'TRY',
            'clientIp' => '127.0.0.1',
        ];
    }

    public function test_postauth_request()
    {
        $request = new PostAuthRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize($this->baseOptions());

        $data = $request->getData();
        $params = $data['request_params'];

        self::assertSame('20000123', $params->paymentId);
        self::assertSame('15.75', $params->paidPrice);
        self::assertSame('TRY', $params->currency);
        self::assertSame('127.0.0.1', $params->ip);
        self::assertSame('https://api.iyzipay.com/payment/postauth', $request->getEndpoint());

        $expected = 'IYZWSv2 ' . Helper::hashV2(
            'sandbox-public',
            'sandbox-private',
            (array) $params,
            'TEST_RAND',
            '/payment/postauth'
        );

        self::assertSame($expected, $data['headers']->Authorization);
    }

    public function test_postauth_partial_capture_trims_price()
    {
        $options = $this->baseOptions();
        $options['amount'] = '10.50'; // partial, less than authorized

        $request = new PostAuthRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize($options);

        $data = $request->getData();

        self::assertSame('10.5', $data['request_params']->paidPrice); // iyzico trimmed format
    }

    public function test_postauth_validation_error_missing_payment_id()
    {
        $options = $this->baseOptions();
        unset($options['paymentId']);

        $request = new PostAuthRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize($options);

        $this->expectException(InvalidRequestException::class);

        $request->getData();
    }

    public function test_postauth_response_success()
    {
        $httpResponse = $this->getMockHttpResponse('PostAuthResponseSuccess.txt');

        $response = new PostAuthResponse($this->getMockRequest(), $httpResponse);

        self::assertTrue($response->isSuccessful());
        self::assertSame('20000123', $response->getTransactionReference());
        self::assertSame('POST_AUTH', $response->getData()->phase);
        // Regression: message/code accessors must not throw on a success body.
        self::assertNull($response->getMessage());
        self::assertNull($response->getCode());
    }

    public function test_postauth_response_wrong_phase_not_successful()
    {
        // A success body in a non-POST_AUTH phase must not read as a completed capture.
        $httpResponse = $this->getMockHttpResponse('PreAuthResponseSuccess.txt'); // phase=PRE_AUTH

        $response = new PostAuthResponse($this->getMockRequest(), $httpResponse);

        self::assertFalse($response->isSuccessful());
    }

    public function test_postauth_response_error()
    {
        $httpResponse = $this->getMockHttpResponse('PostAuthResponseError.txt');

        $response = new PostAuthResponse($this->getMockRequest(), $httpResponse);

        self::assertFalse($response->isSuccessful());
        self::assertSame('5136', $response->getCode());
    }

    public function test_gateway_void_maps_to_cancel()
    {
        $request = $this->gateway->void();

        self::assertInstanceOf(CancelRequest::class, $request);
    }
}
