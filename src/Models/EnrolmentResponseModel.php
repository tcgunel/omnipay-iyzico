<?php

namespace Omnipay\Iyzico\Models;

class EnrolmentResponseModel extends BaseModel
{
    public string $status;
    public string $errorCode;
    public string $errorMessage;
    public string $errorGroup;
    public string $locale;
    public int $systemTime;
    public string $conversationId;
    public string $threeDSHtmlContent;
}
