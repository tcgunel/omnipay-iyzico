<?php

namespace Omnipay\Iyzico\Message;

use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Iyzico\Models\RequestHeadersModel;
use Omnipay\Iyzico\Models\VerifyEnrolmentRequestModel;
use Omnipay\Iyzico\Traits\PurchaseGettersSetters;

class VerifyEnrolmentRequest extends AbstractRequest
{
    use PurchaseGettersSetters;

    /**
     * @return array{request_params: array, headers: RequestHeadersModel}
     *
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     * @throws \Omnipay\Common\Exception\InvalidCreditCardException
     */
    public function getData()
    {
        $this->validateAll();

        $request_params = new VerifyEnrolmentRequestModel([
            'status' => $this->getStatus(),
            'paymentId' => $this->getPaymentId(),
            'conversationData' => $this->getConversationData(),
            'conversationId' => $this->getConversationId(),
            'mdStatus' => $this->getMdStatus(),
        ]);

        return [
            'request_params' => $request_params,
        ];
    }

    protected function validateAll()
    {
        $this->validate(
            'status',
            'paymentId',
            'conversationId',
            'mdStatus',
        );
    }

    /**
     * @throws \Omnipay\Iyzico\Exceptions\OmnipayIyzicoHashValidationException
     * @throws \JsonException
     */
    protected function createResponse($data)
    {
        return $this->response = new VerifyEnrolmentResponse($this, $data['request_params']);
    }

    /**
     * @throws \Omnipay\Iyzico\Exceptions\OmnipayIyzicoHashValidationException
     * @throws \JsonException
     */
    public function sendData($data)
    {
        return $this->createResponse($data);
    }
}
