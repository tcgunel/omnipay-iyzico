<?php

namespace Omnipay\Iyzico\Message;

use Omnipay\Common\Message\RequestInterface;
use Omnipay\Iyzico\Models\VerifyEnrolmentRequestModel;

class VerifyEnrolmentResponse extends RemoteAbstractResponse
{
    protected array $mdStatusMessages = [
        0 => '3-D Secure imzası geçersiz veya doğrulama',
        1 => '',
        2 => 'Kart sahibi veya bankası sisteme kayıtlı değil',
        3 => 'Kartın bankası sisteme kayıtlı değil',
        4 => 'Doğrulama denemesi, kart sahibi sisteme daha sonra kayıt olmayı seçmiş',
        5 => 'Doğrulama yapılamıyor',
        6 => '3-D Secure hatası',
        7 => 'Sistem hatası',
        8 => 'Bilinmeyen kart no',
    ];

    public function __construct(RequestInterface $request, VerifyEnrolmentRequestModel $data)
    {
        parent::__construct($request, $data);

        $this->response = $data;
    }

    public function getData(): VerifyEnrolmentRequestModel
    {
        return $this->response;
    }

    public function isSuccessful(): bool
    {
        return $this->response->mdStatus === 1 && $this->response->status === 'success';
    }

    public function getMessage(): ?string
    {
        return $this->mdStatusMessages[$this->response->mdStatus];
    }

    public function getTransactionId(): ?string
    {
        return $this->response->conversationId;
    }

    public function getCode(): ?string
    {
        return $this->response->mdStatus;
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
