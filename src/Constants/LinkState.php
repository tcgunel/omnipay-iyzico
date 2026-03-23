<?php

namespace Omnipay\Iyzico\Constants;

use ReflectionClass;

class LinkState
{
    public const REQUEST_RECEIVED = 0;
    public const URL_CREATED = 1;
    public const SENT_AND_WAITING_FOR_PAYMENT = 2;
    public const PAYMENT_COMPLETE = 3;
    public const TIMEOUT = 98;
    public const DELETED = 99;

    public function list(): array
    {
        $oClass = new ReflectionClass(__CLASS__);

        return array_values($oClass->getConstants());
    }
}
