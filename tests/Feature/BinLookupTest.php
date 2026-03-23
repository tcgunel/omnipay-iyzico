<?php

namespace Omnipay\Iyzico\Tests\Feature;

use Omnipay\Common\Exception\InvalidCreditCardException;
use Omnipay\Iyzico\Message\BinLookupRequest;
use Omnipay\Iyzico\Message\BinLookupResponse;
use Omnipay\Iyzico\Models\BinLookupRequestModel;
use Omnipay\Iyzico\Models\BinLookupResponseModel;
use Omnipay\Iyzico\Models\RequestHeadersModel;
use Omnipay\Iyzico\Tests\TestCase;

class BinLookupTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_bin_lookup_request()
    {
        $options = file_get_contents(__DIR__ . '/../Mock/BinLookupRequest.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $request = new BinLookupRequest($this->getHttpClient(), $this->getHttpRequest());

        $request->initialize($options);

        $data = $request->getData();

        $expected = [
            'request_params' => new BinLookupRequestModel([
                'price' => '1.0',
                'binNumber' => '545616',
                'conversationId' => '123456',
                'threeD' => true,
            ]),
            'headers' => new RequestHeadersModel([
                'Authorization' => 'IYZWSv2 YXBpS2V5OnNhbmRib3gtcHVibGljJnJhbmRvbUtleTpURVNUX1JBTkQmc2lnbmF0dXJlOmNkOWU5ZWJmNDg2ZjA1NWY4YWI0OTU2NjViZWI2YThiMGM3NTJkNjYzZTIyMzBhNTkzMzA5YTFkMmY1MjlhMWI=',
                'x_iyzi_rnd' => $this->x_iyzi_rnd,
                'x_iyzi_client_version' => $this->x_iyzi_client_version,
            ]),
        ];

        self::assertEquals($expected, $data);
    }

    public function test_bin_lookup_request_validation_error()
    {
        $options = file_get_contents(__DIR__ . '/../Mock/BinLookupRequest-ValidationError.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $request = new BinLookupRequest($this->getHttpClient(), $this->getHttpRequest());

        $request->initialize($options);

        $this->expectException(InvalidCreditCardException::class);

        $request->getData();
    }

    /**
     * @throws \JsonException
     */
    public function test_bin_lookup_response()
    {
        $httpResponse = $this->getMockHttpResponse('BinLookupResponseSuccess.txt');

        $response = new BinLookupResponse($this->getMockRequest(), $httpResponse);

        $data = $response->getData();

        $expected = [
            'status' => 'success',
            'locale' => 'en',
            'systemTime' => 1656319733717,
            'conversationId' => '123456789',
            'installmentDetails' => [
                [
                    'binNumber' => '554960',
                    'price' => 100,
                    'cardType' => 'CREDIT_CARD',
                    'cardAssociation' => 'MASTER_CARD',
                    'cardFamilyName' => 'Bonus',
                    'force3ds' => 0,
                    'bankCode' => 62,
                    'bankName' => 'Garanti Bankasi',
                    'forceCvc' => 0,
                    'commercial' => 0,
                    'installmentPrices' => [
                        [
                            'installmentPrice' => 100,
                            'totalPrice' => 100,
                            'installmentNumber' => 1,
                        ],
                        [
                            'installmentPrice' => 50.53,
                            'totalPrice' => 101.05,
                            'installmentNumber' => 2,
                        ],
                        [
                            'installmentPrice' => 34.04,
                            'totalPrice' => 102.13,
                            'installmentNumber' => 3,
                        ],
                        [
                            'installmentPrice' => 17.2,
                            'totalPrice' => 103.23,
                            'installmentNumber' => 6,
                        ],
                        [
                            'installmentPrice' => 11.85,
                            'totalPrice' => 106.67,
                            'installmentNumber' => 9,
                        ],
                        [
                            'installmentPrice' => 9.2,
                            'totalPrice' => 110.34,
                            'installmentNumber' => 12,
                        ],
                    ],
                ],
            ],
        ];

        $expected['rawResult'] = json_encode($expected, JSON_THROW_ON_ERROR);

        $this->assertTrue($response->isSuccessful());

        $this->assertEquals(new BinLookupResponseModel($expected), $data);

    }

    public function test_charge_response_api_error()
    {
        $httpResponse = $this->getMockHttpResponse('BinLookupResponseApiError.txt');

        $response = new BinLookupResponse($this->getMockRequest(), $httpResponse);

        $data = $response->getData();

        $this->assertFalse($response->isSuccessful());

        $this->assertEquals(new BinLookupResponseModel([
            'status' => 'failure',
            'errorMessage' => 'Bin lookup failed.',
        ]), $data);
    }
}
