<?php

namespace Omnipay\Iyzico\Message;

use Omnipay\Iyzico\Helpers\Helper;
use Omnipay\Iyzico\Models\RequestHeadersModel;

class RefundRequest extends RemoteAbstractRequest
{
    protected $endpoint = '/v2/payment/refund';

    public function getData(): array
    {
        $this->validate('paymentId', 'amount', 'currency');

        $request_params = [
            'locale' => $this->getLanguage(),
            'conversationId' => $this->getConversationId() ?? uniqid('', true),
            'paymentId' => $this->getPaymentId(),
            'price' => $this->getAmount(),
            'currency' => $this->getCurrency(),
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

    protected function createResponse($data): RefundResponse
    {
        return $this->response = new RefundResponse($this, $data);
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
