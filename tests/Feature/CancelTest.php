<?php

namespace Omnipay\Iyzico\Tests\Feature;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Iyzico\Helpers\Helper;
use Omnipay\Iyzico\Message\CancelRequest;
use Omnipay\Iyzico\Message\CancelResponse;
use Omnipay\Iyzico\Models\RequestHeadersModel;
use Omnipay\Iyzico\Tests\TestCase;

class CancelTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     * @throws \JsonException
     */
    public function test_cancel_request(): void
    {
        $options = json_decode(
            file_get_contents(__DIR__ . '/../Mock/CancelRequest.json'),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $request = new CancelRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize($options);

        $data = $request->getData();

        $expectedParams = [
            'locale' => 'tr',
            'conversationId' => 'test-conv-id',
            'paymentId' => '12345',
            'ip' => '127.0.0.1',
        ];

        self::assertEquals($expectedParams, $data['request_params']);
        self::assertInstanceOf(RequestHeadersModel::class, $data['headers']);
        self::assertStringStartsWith('IYZWSv2 ', $data['headers']->Authorization);

        $expectedToken = 'IYZWSv2 ' . Helper::hashV2(
            'sandbox-public',
            'sandbox-private',
            $expectedParams,
            'TEST_RAND',
            '/payment/cancel'
        );
        self::assertEquals($expectedToken, $data['headers']->Authorization);
    }

    public function test_cancel_request_validation_error(): void
    {
        $options = json_decode(
            file_get_contents(__DIR__ . '/../Mock/CancelRequest-ValidationError.json'),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $request = new CancelRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize($options);

        $this->expectException(InvalidRequestException::class);

        $request->getData();
    }

    /**
     * @throws \JsonException
     */
    public function test_cancel_response_success(): void
    {
        $httpResponse = $this->getMockHttpResponse('CancelResponseSuccess.txt');

        $response = new CancelResponse($this->getMockRequest(), $httpResponse);
        $data = $response->getData();

        $this->assertTrue($response->isSuccessful());
        $this->assertNull($response->getMessage());
        $this->assertEquals('12345', $data->paymentId);
        $this->assertEquals('TRY', $data->currency);
        $this->assertEquals('AUTH123', $data->authCode);
        $this->assertEquals('host-ref-123', $data->hostReference);
        $this->assertEquals('cancel-ref-789', $data->cancelHostReference);
    }

    public function test_cancel_response_api_error(): void
    {
        $httpResponse = $this->getMockHttpResponse('CancelResponseApiError.txt');

        $response = new CancelResponse($this->getMockRequest(), $httpResponse);

        $this->assertFalse($response->isSuccessful());
        $this->assertEquals('iptal edilemez', $response->getMessage());
    }
}
