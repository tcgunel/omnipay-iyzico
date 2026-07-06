<?php

namespace Omnipay\Iyzico\Message;

use Omnipay\Iyzico\Helpers\Helper;
use Omnipay\Iyzico\Models\PostAuthRequestModel;
use Omnipay\Iyzico\Models\RequestHeadersModel;

/**
 * Capture (ön otorizasyon kapama / postAuth) a previously placed iyzico hold.
 *
 * POST /payment/postauth with the paymentId returned by the pre-authorization.
 * paidPrice may equal (full) or be less than (partial) the authorized amount.
 * currency is REQUIRED and must match the pre-authorization currency.
 */
class PostAuthRequest extends RemoteAbstractRequest
{
    protected $endpoint = '/payment/postauth';

    /**
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function getData(): array
    {
        $this->validateAll();

        $request_params = new PostAuthRequestModel([
            'locale' => $this->getLanguage(),
            'conversationId' => $this->getConversationId() ?? $this->getTransactionId() ?? uniqid('', true),
            'paymentId' => (string) $this->getPaymentId(),
            'paidPrice' => $this->getAmount(),
            'ip' => $this->getClientIp() ?? '127.0.0.1',
            'currency' => $this->getCurrency(),
        ]);

        return [
            'request_params' => $request_params,
            'headers' => new RequestHeadersModel([
                'Authorization' => $this->token($request_params),
                'x-iyzi-rnd' => $this->getRandomString(),
                'x-iyzi-client-version' => 'tcgunel/omnipay-iyzico:v4.3.0',
            ]),
        ];
    }

    /**
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    protected function validateAll(): void
    {
        $this->validate('language', 'paymentId', 'amount', 'currency', 'privateKey', 'publicKey');
    }

    /**
     * @throws \JsonException
     */
    protected function token($request_model): string
    {
        $appends = (array) $request_model;

        return 'IYZWSv2 ' . Helper::hashV2($this->getPublicKey(), $this->getPrivateKey(), $appends, $this->getRandomString(), $this->endpoint);
    }

    /**
     * @throws \Omnipay\Iyzico\Exceptions\OmnipayIyzicoHashValidationException
     */
    protected function createResponse($data): PostAuthResponse
    {
        return $this->response = new PostAuthResponse($this, $data);
    }

    /**
     * @param array{request_params: PostAuthRequestModel, headers: RequestHeadersModel} $data
     *
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
