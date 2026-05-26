<?php

namespace Omnipay\Iyzico\Message;

use Omnipay\Iyzico\Helpers\Helper;
use Omnipay\Iyzico\Models\RequestHeadersModel;

class CancelRequest extends RemoteAbstractRequest
{
    protected $endpoint = '/payment/cancel';

    public function getData(): array
    {
        $this->validate('paymentId');

        $request_params = [
            'locale' => $this->getLanguage(),
            'conversationId' => $this->getConversationId() ?? uniqid('', true),
            'paymentId' => $this->getPaymentId(),
            'ip' => $this->getClientIp(),
        ];

        return [
            'request_params' => $request_params,
            'headers' => new RequestHeadersModel([
                'Authorization' => $this->token($request_params),
                'x-iyzi-rnd' => $this->getRandomString(),
                'x-iyzi-client-version' => 'tcgunel/omnipay-iyzico:v0.0.1',
            ]),
        ];
    }

    protected function token(array $request_model): string
    {
        return 'IYZWSv2 ' . Helper::hashV2(
            $this->getPublicKey(),
            $this->getPrivateKey(),
            $request_model,
            $this->getRandomString(),
            $this->endpoint
        );
    }

    protected function createResponse($data): CancelResponse
    {
        return $this->response = new CancelResponse($this, $data);
    }

    public function sendData($data)
    {
        $httpResponse = $this->httpClient->request(
            'POST',
            $this->getEndpoint(),
            array_merge($data['headers']->__toArray(), [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]),
            json_encode($data['request_params'])
        );

        return $this->createResponse($httpResponse);
    }
}
