<?php

namespace Omnipay\Iyzico\Message;

use JsonException;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class PaymentInquiryResponse extends AbstractResponse
{
    protected $response;

    protected $request;

    public function __construct(RequestInterface $request, $data)
    {
        parent::__construct($request, $data);

        $this->request = $request;

        $this->response = $data;

        if ($data instanceof ResponseInterface) {

            $body = (string) $data->getBody();

            try {

                $data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

                $data['rawResult'] = preg_replace('/\n+/', '', $body);

                $this->response = $data;

            } catch (JsonException $e) {

                $this->response = [
                    'status' => 'failure',
                    'errorMessage' => $e->getMessage(),
                    'data' => $body,
                ];

            }
        }
    }

    public function isSuccessful(): bool
    {
        return isset($this->response['paymentStatus']) && in_array($this->response['paymentStatus'], ['SUCCESS', 'INIT_CREDIT', 'PENDING_CREDIT']);
    }

    public function getMessage(): string
    {
        return $this->response['errorMessage'];
    }

    public function getData(): array
    {
        return $this->response;
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
