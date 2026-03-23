<?php

namespace Omnipay\Iyzico\Message;

use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Iyzico\Models\PayWithIyzicoResponseModel;

class PayWithIyzicoResponse extends RemoteAbstractResponse implements RedirectResponseInterface
{
    public function __construct(RequestInterface $request, $data)
    {
        parent::__construct($request, $data);

        $this->response = new PayWithIyzicoResponseModel((array) $this->response);
    }

    public function getData(): PayWithIyzicoResponseModel
    {
        return $this->response;
    }

    public function isSuccessful(): bool
    {
        return $this->response->status === 'success';
    }

    public function getMessage(): string
    {
        return $this->response->errorMessage;
    }

    public function isRedirect(): bool
    {
        return $this->response->status === 'success';
    }

    public function getRedirectUrl()
    {
        return $this->response->payWithIyzicoPageUrl;
    }
}
