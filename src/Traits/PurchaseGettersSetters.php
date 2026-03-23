<?php

namespace Omnipay\Iyzico\Traits;

trait PurchaseGettersSetters
{
    public function getPrivateKey()
    {
        return $this->getParameter('privateKey');
    }

    public function setPrivateKey($value)
    {
        return $this->setParameter('privateKey', $value);
    }

    public function getPublicKey()
    {
        return $this->getParameter('publicKey');
    }

    public function setPublicKey($value)
    {
        return $this->setParameter('publicKey', $value);
    }

    public function getLanguage()
    {
        return $this->getParameter('language');
    }

    public function setLanguage($value)
    {
        return $this->setParameter('language', $value);
    }

    public function getSecure()
    {
        return $this->getParameter('secure');
    }

    public function setSecure($value)
    {
        return $this->setParameter('secure', $value);
    }
    public function getEndpoint()
    {
        return ($this->getTestMode() ? 'https://sandbox-api.iyzipay.com' : 'https://api.iyzipay.com') . $this->endpoint;
    }

    public function getRandomString()
    {
        return $this->getParameter('randomString');
    }

    public function setRandomString($value)
    {
        return $this->setParameter('randomString', $value);
    }

    public function getClientIp()
    {
        return $this->getParameter('clientIp');
    }

    public function setClientIp($value)
    {
        return $this->setParameter('clientIp', $value);
    }

    public function getInstallment()
    {
        return $this->getParameter('installment');
    }

    public function setInstallment($value)
    {
        return $this->setParameter('installment', $value);
    }

    public function getBasketId()
    {
        return $this->getParameter('basketId');
    }

    public function setBasketId($value)
    {
        return $this->setParameter('basketId', $value);
    }

    public function getPaymentChannel()
    {
        return $this->getParameter('paymentChannel');
    }

    public function setPaymentChannel($value)
    {
        return $this->setParameter('paymentChannel', $value);
    }

    public function getPaymentGroup()
    {
        return $this->getParameter('paymentGroup');
    }

    public function setPaymentGroup($value)
    {
        return $this->setParameter('paymentGroup', $value);
    }

    public function getRegisterCard()
    {
        return $this->getParameter('registerCard');
    }

    public function setRegisterCard($value)
    {
        return $this->setParameter('registerCard', $value);
    }

    public function getNationalId()
    {
        return $this->getParameter('nationalId');
    }

    public function setNationalId($value)
    {
        return $this->setParameter('nationalId', $value);
    }

    public function getRegistrationDate()
    {
        return $this->getParameter('registrationDate');
    }

    public function setRegistrationDate($value)
    {
        return $this->setParameter('registrationDate', $value);
    }

    public function getLastLoginDate()
    {
        return $this->getParameter('lastLoginDate');
    }

    public function setLastLoginDate($value)
    {
        return $this->setParameter('lastLoginDate', $value);
    }

    public function getUserReference()
    {
        return $this->getParameter('userReference');
    }

    public function setUserReference($value)
    {
        return $this->setParameter('userReference', $value);
    }

    public function getStatus()
    {
        return $this->getParameter('status');
    }

    public function setStatus($value)
    {
        return $this->setParameter('status', $value);
    }

    public function getPaymentId()
    {
        return $this->getParameter('paymentId');
    }

    public function setPaymentId($value)
    {
        return $this->setParameter('paymentId', $value);
    }

    public function getConversationData()
    {
        return $this->getParameter('conversationData');
    }

    public function setConversationData($value)
    {
        return $this->setParameter('conversationData', $value);
    }

    public function getConversationId()
    {
        return $this->getParameter('conversationId');
    }

    public function setConversationId($value)
    {
        return $this->setParameter('conversationId', $value);
    }

    public function getMdStatus()
    {
        return $this->getParameter('mdStatus');
    }

    public function setMdStatus($value)
    {
        return $this->setParameter('mdStatus', $value);
    }

    public function getPaymentSource()
    {
        return $this->getParameter('paymentSource');
    }

    public function setPaymentSource($value)
    {
        return $this->setParameter('paymentSource', $value);
    }

    public function getIsCheckoutInIframe()
    {
        return $this->getParameter('isCheckoutInIframe');
    }

    public function setIsCheckoutInIframe($value)
    {
        return $this->setParameter('isCheckoutInIframe', $value);
    }

    public function getToken()
    {
        return $this->getParameter('token');
    }

    public function setToken($value)
    {
        return $this->setParameter('token', $value);
    }

    public function getEnabledInstallments()
    {
        return $this->getParameter('enabledInstallments');
    }

    public function setEnabledInstallments($value)
    {
        return $this->setParameter('enabledInstallments', $value);
    }
}
