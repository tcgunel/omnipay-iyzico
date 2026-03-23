<?php

namespace Omnipay\Iyzico\Models;

class PurchaserModel extends BaseModel
{
    public string $id;
    public string $name;
    public string $surname;
    public string $identityNumber;
    public string $email;
    public string $gsmNumber;
    public string $registrationDate;
    public string $lastLoginDate;
    public string $registrationAddress;
    public string $city;
    public string $country;
    public string $zipCode;
    public string $ip;
}
