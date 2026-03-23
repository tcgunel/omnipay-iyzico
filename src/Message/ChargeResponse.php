<?php

namespace Omnipay\Iyzico\Message;

use Omnipay\Common\Message\RequestInterface;
use Omnipay\Iyzico\Models\ChargeResponseModel;

class ChargeResponse extends RemoteAbstractResponse
{
    public function __construct(RequestInterface $request, $data)
    {
        parent::__construct($request, $data);

        $this->response = new ChargeResponseModel((array) $this->response);
    }

    public function getData(): ChargeResponseModel
    {
        return $this->response;
    }

    public function isSuccessful(): bool
    {
        return $this->response->status === 'success';
    }

    public function getMessage(): ?string
    {
        return $this->response->errorMessage;
    }

    public function getTransactionId(): ?string
    {
        return $this->response->conversationId;
    }

    public function getCode(): ?string
    {
        return $this->response->errorCode;
    }

    public function getRedirectData()
    {
        return null;
    }

    public function getRedirectUrl(): string
    {
        return '';
    }
}
