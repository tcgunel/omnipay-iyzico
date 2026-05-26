<?php

namespace Omnipay\Iyzico\Message;

use JsonException;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Iyzico\Models\RefundResponseModel;
use Psr\Http\Message\ResponseInterface;

class RefundResponse extends AbstractResponse
{
    protected $response;

    public function __construct(RequestInterface $request, $data)
    {
        parent::__construct($request, $data);

        $this->response = $data;

        if ($data instanceof ResponseInterface) {
            $body = (string) $data->getBody();

            try {
                $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
                $decoded['rawResult'] = preg_replace('/\n+/', '', $body);
                $this->response = $decoded;
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
        return isset($this->response['status']) && $this->response['status'] === 'success';
    }

    public function getMessage(): ?string
    {
        return $this->response['errorMessage'] ?? null;
    }

    public function getData(): RefundResponseModel
    {
        return new RefundResponseModel($this->response);
    }
}
