<?php

namespace Omnipay\Iyzico\Message;

use JsonException;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Iyzico\Exceptions\OmnipayIyzicoBinLookupException;
use Omnipay\Iyzico\Models\BinLookupResponseModel;
use Psr\Http\Message\ResponseInterface;

class BinLookupResponse extends AbstractResponse
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

                $this->response = new BinLookupResponseModel($data);

                if (!$this->isSuccessful()) {

                    throw new OmnipayIyzicoBinLookupException('Bin lookup failed.');

                }

            } catch (JsonException $e) {

                $this->response = new BinLookupResponseModel([
                    'status' => 'failure',
                    'errorMessage' => $body,
                ]);

            } catch (OmnipayIyzicoBinLookupException $e) {

                $this->response = new BinLookupResponseModel([
                    'status' => 'failure',
                    'errorMessage' => $e->getMessage(),
                ]);

            }

        }
    }

    public function isSuccessful(): bool
    {
        return $this->response->status === 'success';
    }

    public function getMessage(): string
    {
        return $this->response->errorMessage;
    }

    public function getData(): BinLookupResponseModel
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
