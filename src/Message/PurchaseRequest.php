<?php

namespace Omnipay\Iyzico\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Item;
use Omnipay\Iyzico\Helpers\Helper;
use Omnipay\Iyzico\Models\AddressModel;
use Omnipay\Iyzico\Models\ChargeRequestModel;
use Omnipay\Iyzico\Models\EnrolmentRequestModel;
use Omnipay\Iyzico\Models\PaymentCard;
use Omnipay\Iyzico\Models\ProductModel;
use Omnipay\Iyzico\Models\PurchaserModel;
use Omnipay\Iyzico\Models\RequestHeadersModel;
use Omnipay\Iyzico\Traits\PurchaseGettersSetters;

class PurchaseRequest extends RemoteAbstractRequest
{
    use PurchaseGettersSetters;

    /**
     * @return array{request_params: array, headers: RequestHeadersModel}
     *
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     * @throws \Omnipay\Common\Exception\InvalidCreditCardException
     */
    public function getData()
    {
        $this->validateAll();

        $request_params = new ChargeRequestModel([
            'locale' => $this->getLanguage(),
            'conversationId' => $this->getTransactionId(),
            'price' => array_sum(array_map(static fn (Item $item) => $item->getPrice(), $this->getItems()?->all())),
            'paidPrice' => $this->getAmount(),
            'currency' => $this->getCurrency(),
            'installment' => $this->getInstallment() ?? '1',
            'basketId' => $this->getBasketId() ?? $this->getTransactionId(),
            'paymentChannel' => $this->getPaymentChannel(),
            'paymentGroup' => $this->getPaymentGroup(),
            'paymentSource' => $this->getPaymentSource() ?? '',
            'callbackUrl' => $this->getReturnUrl(),

            'paymentCard' => new PaymentCard([
                'cardNumber' => $this->get_card('getNumber'),
                'expireYear' => $this->get_card('getExpiryYear'),
                'expireMonth' => $this->get_card('getExpiryMonth'),
                'cvc' => $this->get_card('getCvv'),
                'cardHolderName' => $this->get_card('getName'),
                'registerCard' => $this->getRegisterCard() ?? 0,
            ]),

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
                'zipCode' => $this->get_card('getShippingPostcode') ?? '10000',
                'registrationDate' => $this->getRegistrationDate() ?? date('Y-m-d H:i:s'),
                'lastLoginDate' => $this->getLastLoginDate() ?? date('Y-m-d H:i:s'),
            ]),

            'billingAddress' => new AddressModel([

                'contactName' => $this->get_card('getBillingName'),
                'city' => $this->get_card('getBillingCity'),
                'country' => $this->get_card('getBillingCountry'),
                'address' => trim(implode(' ', [$this->get_card('getBillingAddress1'), $this->get_card('getBillingAddress2')])),
                'zipCode' => $this->get_card('getBillingPostcode') ?? '10000',

            ]),

            'shippingAddress' => new AddressModel([

                'contactName' => $this->get_card('getShippingName'),
                'city' => $this->get_card('getShippingCity'),
                'country' => $this->get_card('getShippingCountry'),
                'address' => trim(implode(' ', [$this->get_card('getShippingAddress1'), $this->get_card('getShippingAddress2')])),
                'zipCode' => $this->get_card('getShippingPostcode') ?? '10000',

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
                'x-iyzi-client-version' => 'tcgunel/omnipay-iyzico:v1.0.7',
            ]),
        ];
    }

    protected function validateAll()
    {
        $this->getCard()->validate();

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
     * @param ChargeRequestModel|EnrolmentRequestModel $request_model
     *
     * @return string
     * @throws \JsonException
     */
    protected function token($request_model): string
    {
        $appends = (array) $request_model;

        return 'IYZWSv2 ' . Helper::hashV2($this->getPublicKey(), $this->getPrivateKey(), $appends, $this->getRandomString(), $this->endpoint);
    }

    protected function createResponse($data)
    {
        // overridden by child class
    }

    public function sendData($data)
    {
        // overridden by child class
    }
}
