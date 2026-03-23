<?php

namespace Omnipay\Iyzico\Message;

use Omnipay\Iyzico\Models\EnrolmentRequestModel;
use Omnipay\Iyzico\Models\RequestHeadersModel;

/**
 * Iyzico 3D Secure enrolment request
 */
class EnrolmentRequest extends PurchaseRequest
{
    protected $endpoint = '/payment/3dsecure/initialize';

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
        $data = parent::getData();

        $this->validate('secure', 'returnUrl');

        $data['request_params'] = (array) $data['request_params'];

        $data['request_params']['callbackUrl'] = $this->getReturnUrl();

        $data['request_params'] = new EnrolmentRequestModel($data['request_params']);

        $data['headers']->Authorization = $this->token($data['request_params']);

        return $data;
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

    protected function createResponse($data): EnrolmentResponse
    {
        return $this->response = new EnrolmentResponse($this, $data);
    }
}
