<?php

namespace Omnipay\Iyzico\Message;

use Omnipay\Common\Message\RequestInterface;
use Omnipay\Iyzico\Models\CompletePurchaseResponseModel;

class CompletePurchaseResponse extends RemoteAbstractResponse
{
    public function __construct(RequestInterface $request, $data)
    {
        parent::__construct($request, $data);

        $this->response = new CompletePurchaseResponseModel((array) $this->response);
    }

    public function getData(): CompletePurchaseResponseModel
    {
        return $this->response;
    }

    public function isSuccessful(): bool
    {
        return $this->response->status === 'success';
    }

    public function getMessage(): ?string
    {
        // Typed, non-nullable props are absent on success responses; ?? avoids a
        // fatal "must not be accessed before initialization" Error.
        return $this->response->errorMessage ?? null;
    }

    public function getTransactionId(): ?string
    {
        return $this->response->conversationId ?? null;
    }

    public function getCode(): ?string
    {
        return $this->response->errorCode ?? null;
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
