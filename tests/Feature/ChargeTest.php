<?php

namespace Omnipay\Iyzico\Tests\Feature;

use Omnipay\Common\Exception\InvalidCreditCardException;
use Omnipay\Iyzico\Message\ChargeRequest;
use Omnipay\Iyzico\Message\ChargeResponse;
use Omnipay\Iyzico\Models\AddressModel;
use Omnipay\Iyzico\Models\ChargeRequestModel;
use Omnipay\Iyzico\Models\ChargeResponseModel;
use Omnipay\Iyzico\Models\PaymentCard;
use Omnipay\Iyzico\Models\ProductModel;
use Omnipay\Iyzico\Models\PurchaserModel;
use Omnipay\Iyzico\Models\RequestHeadersModel;
use Omnipay\Iyzico\Tests\TestCase;

class ChargeTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();


    }

    public function test_charge_request()
    {
        $options = file_get_contents(__DIR__ . '/../Mock/ChargeRequest.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $request = new ChargeRequest($this->getHttpClient(), $this->getHttpRequest());

        $request->initialize($options);

        $data = $request->getData();

        $expected = [
            'request_params' => new ChargeRequestModel([
                'locale' => 'tr',
                'conversationId' => '123456789',
                'price' => 60,
                'paidPrice' => 150,
                'currency' => 'TRY',
                'installment' => 1,
                'basketId' => '123456789',
                'paymentChannel' => 'WEB',
                'paymentGroup' => 'PRODUCT',
                'paymentSource' => '',

                'paymentCard' => new PaymentCard([

                    'cardNumber' => '5528790000000008',
                    'expireYear' => 2030,
                    'expireMonth' => 12,
                    'cvc' => '123',
                    'cardHolderName' => 'Example User',
                    'registerCard' => 0,
                ]),

                'buyer' => new PurchaserModel([

                    'id' => '1',
                    'name' => 'Example',
                    'surname' => 'User',
                    'identityNumber' => '11111111111',
                    'city' => 'Shipstown',
                    'country' => 'TR',
                    'email' => 'abc@msn.com',
                    'gsmNumber' => '+905554443322',
                    'ip' => '127.0.0.1',
                    'registrationAddress' => '123 Shipping St Shipsville',
                    'zipCode' => '54321',
                    'registrationDate' => date('Y-m-d H:i:s'),
                    'lastLoginDate' => date('Y-m-d H:i:s'),
                ]),

                'billingAddress' => new AddressModel([

                    'contactName' => 'Example User',
                    'city' => 'Billstown',
                    'country' => 'TR',
                    'address' => '123 Billing St Billsville',
                    'zipCode' => '12345',

                ]),

                'shippingAddress' => new AddressModel([

                    'contactName' => 'Example User',
                    'city' => 'Shipstown',
                    'country' => 'TR',
                    'address' => '123 Shipping St Shipsville',
                    'zipCode' => '54321',

                ]),
                'basketItems' => [
                    new ProductModel([
                        'id' => 'fe77183442f200b82d2fe75c35c4b1e5e85eafd4',
                        'price' => 20,
                        'name' => 'Perspiciatis et facilis tempore facilis.',
                        'category1' => '-',
                        'category2' => '-',
                        'itemType' => 'PHYSICAL',
                    ]),
                    new ProductModel([
                        'id' => '574dfd56acb0a792a7b15b000e9753aa18ee3b5d',
                        'price' => 40,
                        'name' => 'Perspiciatis et facilis tempore facilis.',
                        'category1' => '-',
                        'category2' => '-',
                        'itemType' => 'PHYSICAL',
                    ]),
                ],
            ]),
            'headers' => new RequestHeadersModel([
                'Authorization' => $data['headers']->Authorization,
                'x_iyzi_rnd' => $this->x_iyzi_rnd,
                'x_iyzi_client_version' => $this->x_iyzi_client_version,
            ]),
        ];

        self::assertStringStartsWith('IYZWSv2 ', $data['headers']->Authorization);
        self::assertSame(json_encode($expected), json_encode($data));
    }

    public function test_charge_request_validation_error()
    {
        $options = file_get_contents(__DIR__ . '/../Mock/ChargeRequest-ValidationError.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $request = new ChargeRequest($this->getHttpClient(), $this->getHttpRequest());

        $request->initialize($options);

        $this->expectException(InvalidCreditCardException::class);

        $request->getData();
    }

    public function test_charge_response()
    {
        $httpResponse = $this->getMockHttpResponse('ChargeResponseSuccess.txt');

        $response = new ChargeResponse($this->getMockRequest(), $httpResponse);

        $data = $response->getData();

        $this->assertTrue($response->isSuccessful());

        $this->assertEquals(new ChargeResponseModel([
            'status' => 'success',
            'locale' => 'tr',
            'systemTime' => 1657782727657,
            'conversationId' => '123456789',
            'price' => 1,
            'paidPrice' => 1.2,
            'installment' => 1,
            'paymentId' => 18003415,
            'fraudStatus' => 1,
            'merchantCommissionRate' => 20,
            'merchantCommissionRateAmount' => 0.2,
            'iyziCommissionRateAmount' => 0.048,
            'iyziCommissionFee' => 0.25,
            'cardType' => 'CREDIT_CARD',
            'cardAssociation' => 'MASTER_CARD',
            'cardFamily' => 'Paraf',
            'binNumber' => 552879,
            'lastFourDigits' => 8,
            'basketId' => 'B67832',
            'currency' => 'TRY',
            'itemTransactions' => [
                [
                    'itemId' => '5aebc45ee35df4bb145bcc3a42abc4d8ccf2bebf',
                    'paymentTransactionId' => 19217173,
                    'transactionStatus' => 2,
                    'price' => 0.3,
                    'paidPrice' => 0.36,
                    'merchantCommissionRate' => 20,
                    'merchantCommissionRateAmount' => 0.06,
                    'iyziCommissionRateAmount' => 0.0144,
                    'iyziCommissionFee' => 0.075,
                    'blockageRate' => 0,
                    'blockageRateAmountMerchant' => 0,
                    'blockageRateAmountSubMerchant' => 0,
                    'blockageResolvedDate' => '2022-07-22 00:00:00',
                    'subMerchantPrice' => 0,
                    'subMerchantPayoutRate' => 0,
                    'subMerchantPayoutAmount' => 0,
                    'merchantPayoutAmount' => 0.2706,
                    'convertedPayout' => [
                        'paidPrice' => 0.36,
                        'iyziCommissionRateAmount' => 0.0144,
                        'iyziCommissionFee' => 0.075,
                        'blockageRateAmountMerchant' => 0,
                        'blockageRateAmountSubMerchant' => 0,
                        'subMerchantPayoutAmount' => 0,
                        'merchantPayoutAmount' => 0.2706,
                        'iyziConversionRate' => 0,
                        'iyziConversionRateAmount' => 0,
                        'currency' => 'TRY',
                    ],
                ],
                [
                    'itemId' => 'c84d6f45d08693ca2ad41f5b927da276ebc944d5',
                    'paymentTransactionId' => 19217174,
                    'transactionStatus' => 2,
                    'price' => 0.5,
                    'paidPrice' => 0.6,
                    'merchantCommissionRate' => 20,
                    'merchantCommissionRateAmount' => 0.1,
                    'iyziCommissionRateAmount' => 0.024,
                    'iyziCommissionFee' => 0.125,
                    'blockageRate' => 0,
                    'blockageRateAmountMerchant' => 0,
                    'blockageRateAmountSubMerchant' => 0,
                    'blockageResolvedDate' => '2022-07-22 00:00:00',
                    'subMerchantPrice' => 0,
                    'subMerchantPayoutRate' => 0,
                    'subMerchantPayoutAmount' => 0,
                    'merchantPayoutAmount' => 0.451,
                    'convertedPayout' => [
                        'paidPrice' => 0.6,
                        'iyziCommissionRateAmount' => 0.024,
                        'iyziCommissionFee' => 0.125,
                        'blockageRateAmountMerchant' => 0,
                        'blockageRateAmountSubMerchant' => 0,
                        'subMerchantPayoutAmount' => 0,
                        'merchantPayoutAmount' => 0.451,
                        'iyziConversionRate' => 0,
                        'iyziConversionRateAmount' => 0,
                        'currency' => 'TRY',
                    ],
                ],
                [
                    'itemId' => '2ff7c75dd9510387e4751a1289e6020e504e383b',
                    'paymentTransactionId' => 19217178,
                    'transactionStatus' => 2,
                    'price' => 0.2,
                    'paidPrice' => 0.24,
                    'merchantCommissionRate' => 20,
                    'merchantCommissionRateAmount' => 0.04,
                    'iyziCommissionRateAmount' => 0.0096,
                    'iyziCommissionFee' => 0.05,
                    'blockageRate' => 0,
                    'blockageRateAmountMerchant' => 0,
                    'blockageRateAmountSubMerchant' => 0,
                    'blockageResolvedDate' => '2022-07-22 00:00:00',
                    'subMerchantPrice' => 0,
                    'subMerchantPayoutRate' => 0,
                    'subMerchantPayoutAmount' => 0,
                    'merchantPayoutAmount' => 0.1804,
                    'convertedPayout' => [
                        'paidPrice' => 0.24,
                        'iyziCommissionRateAmount' => 0.0096,
                        'iyziCommissionFee' => 0.05,
                        'blockageRateAmountMerchant' => 0,
                        'blockageRateAmountSubMerchant' => 0,
                        'subMerchantPayoutAmount' => 0,
                        'merchantPayoutAmount' => 0.1804,
                        'iyziConversionRate' => 0,
                        'iyziConversionRateAmount' => 0,
                        'currency' => 'TRY',
                    ],
                ],
            ],
            'authCode' => 934393,
            'phase' => 'AUTH',
            'hostReference' => 'mock00001iyzihostrfn',
        ]), $data);
    }

    public function test_charge_response_api_error()
    {
        $httpResponse = $this->getMockHttpResponse('ChargeResponseApiError.txt');

        $response = new ChargeResponse($this->getMockRequest(), $httpResponse);

        $this->assertFalse($response->isSuccessful());

        $this->assertEquals('1000', $response->getCode());

        $this->assertEquals('Geçersiz imza', $response->getMessage());
    }
}
