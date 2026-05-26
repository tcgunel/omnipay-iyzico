<?php

namespace Omnipay\Iyzico\Tests\Feature;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Iyzico\Helpers\Helper;
use Omnipay\Iyzico\Message\RefundRequest;
use Omnipay\Iyzico\Message\RefundResponse;
use Omnipay\Iyzico\Models\RequestHeadersModel;
use Omnipay\Iyzico\Tests\TestCase;

class RefundTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     * @throws \JsonException
     */
    public function test_refund_request(): void
    {
        $options = json_decode(
            file_get_contents(__DIR__ . '/../Mock/RefundRequest.json'),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $request = new RefundRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize($options);

        $data = $request->getData();

        $expectedParams = [
            'locale' => 'tr',
            'conversationId' => 'test-conv-id',
            'paymentId' => '12345',
            'price' => '12.34',
            'currency' => 'TRY',
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
            '/v2/payment/refund'
        );
        self::assertEquals($expectedToken, $data['headers']->Authorization);
    }

    public function test_refund_request_validation_error(): void
    {
        $options = json_decode(
            file_get_contents(__DIR__ . '/../Mock/RefundRequest-ValidationError.json'),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $request = new RefundRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize($options);

        $this->expectException(InvalidRequestException::class);

        $request->getData();
    }

    /**
     * @throws \JsonException
     */
    public function test_refund_response_success(): void
    {
        $httpResponse = $this->getMockHttpResponse('RefundResponseSuccess.txt');

        $response = new RefundResponse($this->getMockRequest(), $httpResponse);
        $data = $response->getData();

        $this->assertTrue($response->isSuccessful());
        $this->assertNull($response->getMessage());
        $this->assertEquals('12345', $data->paymentId);
        $this->assertEquals('TRY', $data->currency);
        $this->assertEquals('AUTH123', $data->authCode);
        $this->assertEquals('host-ref-123', $data->hostReference);
        $this->assertEquals('refund-ref-456', $data->refundHostReference);
    }

    public function test_refund_response_api_error(): void
    {
        $httpResponse = $this->getMockHttpResponse('RefundResponseApiError.txt');

        $response = new RefundResponse($this->getMockRequest(), $httpResponse);

        $this->assertFalse($response->isSuccessful());
        $this->assertEquals('iadeye uygun degil', $response->getMessage());
    }
}
