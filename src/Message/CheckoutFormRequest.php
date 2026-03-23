<?php

namespace Omnipay\Iyzico\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Item;
use Omnipay\Iyzico\Helpers\Helper;
use Omnipay\Iyzico\Models\AddressModel;
use Omnipay\Iyzico\Models\CheckoutFormRequestModel;
use Omnipay\Iyzico\Models\ProductModel;
use Omnipay\Iyzico\Models\PurchaserModel;
use Omnipay\Iyzico\Models\RequestHeadersModel;

class CheckoutFormRequest extends RemoteAbstractRequest
{
    protected $endpoint = '/payment/iyzipos/checkoutform/initialize/auth/ecom';

    /**
     * Get the XML registration string to be sent to the gateway
     *
     * @return array{request_params: array, headers: RequestHeadersModel}
     *
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     * @throws \Omnipay\Common\Exception\InvalidCreditCardException
     */
    public function getData()
    {
        $this->validateAll();

        $request_params = new CheckoutFormRequestModel([
            'locale' => $this->getLanguage(),
            'conversationId' => $this->getTransactionId(),
            'price' => array_sum(array_map(static fn (Item $item) => $item->getPrice(), $this->getItems()?->all())),
            'paidPrice' => $this->getAmount(),
            'currency' => $this->getCurrency(),
            'basketId' => $this->getBasketId() ?? $this->getTransactionId(),
            'paymentGroup' => $this->getPaymentGroup(),
            'paymentSource' => $this->getPaymentSource() ?? '',
            'callbackUrl' => $this->getReturnUrl(),
            'enabledInstallments' => $this->getEnabledInstallments(),

            'buyer' => new PurchaserModel([

                'id' => $this->getUserReference(),
                'name' => $this->get_card('getBillingFirstName'),
                'surname' => $this->get_card('getBillingLastName'),
                'identityNumber' => $this->getNationalId() ?? '11111111111',
                'city' => $this->get_card('getShippingCity'),
                'country' => $this->get_card('getShippingCountry'),
                'email' => $this->get_card('getEmail') ?? 'required-dummy@email.com',
                'gsmNumber' => implode('', [$this->get_card('getPhoneExtension'), $this->get_card('getPhone')]),
                'ip' => $this->getClientIp() ?? '127.0.0.1',
                'registrationAddress' => trim(implode(' ', [$this->get_card('getShippingAddress1'), $this->get_card('getShippingAddress2')])),
            ]),

            'billingAddress' => new AddressModel([

                'contactName' => $this->get_card('getBillingName'),
                'city' => $this->get_card('getBillingCity'),
                'country' => $this->get_card('getBillingCountry'),
                'address' => trim(implode(' ', [$this->get_card('getBillingAddress1'), $this->get_card('getBillingAddress2')])),

            ]),

            'shippingAddress' => new AddressModel([

                'contactName' => $this->get_card('getShippingName'),
                'city' => $this->get_card('getShippingCity'),
                'country' => $this->get_card('getShippingCountry'),
                'address' => trim(implode(' ', [$this->get_card('getShippingAddress1'), $this->get_card('getShippingAddress2')])),

            ]),

            'basketItems' => array_map(function (Item $item) {
                return new ProductModel([
                    'id' => hash('sha1', $item->getName() . $item->getPrice()),
                    'price' => $item->getPrice(),
                    'name' => $item->getName(),
                    'category1' => '-',
                    'category2' => '-',
                    'itemType' => 'PHYSICAL',
                ]);
            }, $this->getItems()?->all()),

        ]);

        return [
            'request_params' => $request_params,
            'headers' => new RequestHeadersModel([
                'Authorization' => $this->token($request_params),
                'x-iyzi-rnd' => $this->getRandomString(),
                'x-iyzi-client-version' => 'tcgunel/omnipay-iyzico:v0.0.1',
            ]),
        ];
    }
    /**
     * @throws \JsonException
     */
    protected function token(CheckoutFormRequestModel $request_model): string
    {
        $appends = json_decode(json_encode($request_model, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);

        return 'IYZWSv2 ' . Helper::hashV2($this->getPublicKey(), $this->getPrivateKey(), $appends, $this->getRandomString(), $this->endpoint);
    }

    protected function validateAll()
    {
        $this->validateAdditionalCardFields(
            'email',
            'billingName',
            'billingCity',
            'billingCountry',
            'billingAddress1',
            'shippingName',
            'shippingCity',
            'shippingCountry',
            'shippingAddress1',
        );

        $this->validate(
            'amount',
            'currency',
            'installment',
            'userReference',
            'nationalId',
            'clientIp',
            'items',
            'privateKey',
            'publicKey',
        );
    }

    protected function validateAdditionalCardFields(...$args)
    {
        $card = $this->getCard();

        foreach ($args as $key) {

            $value = $card->{'get' . ucfirst($key)}();

            if (!isset($value)) {

                throw new InvalidRequestException("The cards $key parameter is required");

            }

        }
    }

    /**
     * @throws \Omnipay\Iyzico\Exceptions\OmnipayIyzicoHashValidationException
     */
    public function sendData($data)
    {
        $httpResponse = $this->httpClient->request(
            'POST',
            $this->getEndpoint(),
            array_merge($data['headers']->__toArray(), [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]),
            json_encode($data['request_params'], JSON_THROW_ON_ERROR)
        );

        return $this->createResponse($httpResponse);
    }

    protected function createResponse($data): CheckoutFormResponse
    {
        return $this->response = new CheckoutFormResponse($this, $data);
    }
}
