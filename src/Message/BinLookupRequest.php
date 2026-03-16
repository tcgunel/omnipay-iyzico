<?php

namespace Omnipay\Iyzico\Message;

use Omnipay\Common\Exception\InvalidCreditCardException;
use Omnipay\Iyzico\Helpers\Helper;
use Omnipay\Iyzico\Models\BinLookupRequestModel;
use Omnipay\Iyzico\Models\RequestHeadersModel;

class BinLookupRequest extends RemoteAbstractRequest
{
    protected $endpoint = '/payment/iyzipos/installment';

    /**
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     * @throws InvalidCreditCardException
     */
    public function getData()
    {
        $this->validateAll();

        $request_params = new BinLookupRequestModel([
            "binNumber"      => $this->getCard() ? $this->getCard()->getNumber() : null,
            "conversationId" => $this->getTransactionId(),
            "locale"         => $this->getLanguage(),
            "price"          => $this->getAmount(),
        ]);

        return [
            "request_params" => $request_params,
            "headers"        => new RequestHeadersModel([
                "Authorization"         => $this->token($request_params),
                "x-iyzi-rnd"            => $this->getRandomString(),
                "x-iyzi-client-version" => 'tcgunel/omnipay-iyzico:v0.0.1',
            ]),
        ];
    }

    /**
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     * @throws InvalidCreditCardException
     */
    protected function validateAll(): void
    {
        $this->validate("amount", "transactionId");

        if ($this->getCard() !== null && !is_null($this->getCard()->getNumber()) && !preg_match('/^\d{6,19}$/', $this->getCard()->getNumber())) {
            throw new InvalidCreditCardException('Card number should have at least 6 to maximum of 19 digits');
        }
    }

    /**
     * @param BinLookupRequestModel $request_model
     *
     * @return string
     */
    protected function token(BinLookupRequestModel $request_model): string
    {
        $appends = (array)$request_model;

        return 'IYZWSv2 ' . Helper::hashV2($this->getPublicKey(), $this->getPrivateKey(), $appends, $this->getRandomString(), $this->endpoint);
    }

    /**
     * @throws \JsonException
     */
    protected function createResponse($data): BinLookupResponse
    {
        return $this->response = new BinLookupResponse($this, $data);
    }

    public function sendData($data)
    {
        $httpResponse = $this->httpClient->request(
            'POST',
            $this->getEndpoint(),
            array_merge($data["headers"]->__toArray(), [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ]),
            json_encode($data["request_params"])
        );

        return $this->createResponse($httpResponse);
    }
}
