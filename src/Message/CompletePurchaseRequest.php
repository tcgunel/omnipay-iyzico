<?php

namespace Omnipay\Iyzico\Message;

use Omnipay\Iyzico\Helpers\Helper;
use Omnipay\Iyzico\Models\CompletePurchaseRequestModel;
use Omnipay\Iyzico\Models\RequestHeadersModel;
use Omnipay\Iyzico\Traits\PurchaseGettersSetters;

class CompletePurchaseRequest extends RemoteAbstractRequest
{
    use PurchaseGettersSetters;

    protected $endpoint = '/payment/3dsecure/auth';

    /**
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function getData()
    {
        $this->validateAll();

        $request_params = new CompletePurchaseRequestModel([
            'locale' => $this->getLanguage(),
            'conversationId' => $this->getTransactionId(),
            'paymentId' => $this->getPaymentId(),
            'conversationData' => $this->getConversationData() ?? '',
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
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    protected function validateAll(): void
    {
        $this->validate('language', 'transactionId', 'paymentId');
    }

    /**
     * @param CompletePurchaseRequestModel $request_model
     *
     * @return string
     * @throws \JsonException
     */
    protected function token($request_model): string
    {
        $appends = (array) $request_model;

        return 'IYZWSv2 ' . Helper::hashV2($this->getPublicKey(), $this->getPrivateKey(), $appends, $this->getRandomString(), $this->endpoint);
    }

    /**
     * @throws \Omnipay\Iyzico\Exceptions\OmnipayIyzicoHashValidationException
     * @throws \JsonException
     */
    protected function createResponse($data): CompletePurchaseResponse
    {
        return $this->response = new CompletePurchaseResponse($this, $data);
    }

    /**
     * @param array{request_params: CompletePurchaseRequestModel, headers: RequestHeadersModel} $data
     *
     * @return CompletePurchaseResponse
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
