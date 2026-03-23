<?php

namespace Omnipay\Iyzico\Message;

use Omnipay\Common\Exception\InvalidCreditCardException;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Iyzico\Models\ChargeRequestModel;
use Omnipay\Iyzico\Models\RequestHeadersModel;

class ChargeRequest extends PurchaseRequest
{
    protected $endpoint = '/payment/auth';

    /**
     * @return array{request_params: ChargeRequestModel, headers: RequestHeadersModel}
     * @throws InvalidCreditCardException
     * @throws InvalidRequestException
     */
    public function getData(): array
    {
        $data = parent::getData();

        $data['request_params'] = new ChargeRequestModel((array) $data['request_params']);

        return $data;
    }

    protected function validateAll()
    {
        /*if ($this->getCardReference() && $this->getUserReference()) {

            $this->validate("cardReference", "userReference");

        } else {*/

        $this->getCard()->validate();

        //        }

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

    /**
     * @throws \Omnipay\Iyzico\Exceptions\OmnipayIyzicoHashValidationException
     */
    protected function createResponse($data): ChargeResponse
    {
        return $this->response = new ChargeResponse($this, $data);
    }

    /**
     * @param array{request_params: ChargeRequestModel, headers: RequestHeadersModel} $data
     *
     * @return ChargeResponse
     * @throws \Omnipay\Iyzico\Exceptions\OmnipayIyzicoHashValidationException
     * @throws \JsonException
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
}
