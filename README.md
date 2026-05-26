[![License](https://poser.pugx.org/tcgunel/omnipay-iyzico/license)](https://packagist.org/packages/tcgunel/omnipay-iyzico)
[![Buy us a tree](https://img.shields.io/badge/Treeware-%F0%9F%8C%B3-lightgreen)](https://plant.treeware.earth/tcgunel/omnipay-iyzico)
[![PHP Composer](https://github.com/tcgunel/omnipay-iyzico/actions/workflows/tests.yml/badge.svg)](https://github.com/tcgunel/omnipay-iyzico/actions/workflows/tests.yml)

# Omnipay Iyzico Gateway
Omnipay gateway for Iyzico. All the methods of Iyzico implemented for easy usage.

## Requirements
| PHP   | Package |
|-------|---------|
| ^8.3  | v2.x    |

## Installation

```
composer require tcgunel/omnipay-iyzico
```

## Usage

```php
$gateway = Omnipay::create('Iyzico');
$gateway->setPublicKey('your-public-key');
$gateway->setPrivateKey('your-private-key');
$gateway->setTestMode(true); // sandbox
```

## Methods

#### Payment Services

* `purchase($options)` — Direct charge (non-3DS) or 3D Secure enrolment
* `completePurchase($options)` — Complete a 3DS checkout form payment
* `verifyEnrolment($options)` — Verify 3DS bank callback (local check, no HTTP)
* `paymentInquiry($options)` — Query payment status by `paymentId` / `conversationId`
* `checkoutForm($options)` — iyzico hosted checkout form
* `checkoutFormInquiry($options)` — Query hosted checkout form status
* `payWithIyzico($options)` — Pay with iyzico wallet
* `binLookup($options)` — BIN / installment lookup

#### Refund & Cancel

* `refund($options)` — Refund a captured payment via `POST /v2/payment/refund`. Accepts `paymentId` (top-level) + `price` + `currency`; iyzico auto-selects the sub-transaction. Supports partial and full refunds.

  ```php
  $response = $gateway->refund([
      'paymentId'  => 'iyzico-payment-id',
      'amount'     => '12.34',
      'currency'   => 'TRY',
      'language'   => 'tr',
      'clientIp'   => request()->ip(),
  ])->send();

  $response->isSuccessful(); // true / false
  $response->getMessage();   // error message on failure
  $data = $response->getData(); // RefundResponseModel
  ```

* `cancel($options)` — Void a same-day payment via `POST /payment/cancel`. Full void only (no partial amounts); operates on `paymentId`. Only valid before settlement.

  ```php
  $response = $gateway->cancel([
      'paymentId' => 'iyzico-payment-id',
      'language'  => 'tr',
      'clientIp'  => request()->ip(),
  ])->send();

  $response->isSuccessful();
  $data = $response->getData(); // CancelResponseModel
  ```

## Tests
```
composer test
```
For Windows:
```
vendor\bin\paratest.bat
```

## Treeware

This package is [Treeware](https://treeware.earth). If you use it in production, then we ask that you [**buy the world a tree**](https://plant.treeware.earth/tcgunel/omnipay-iyzico) to thank us for our work. By contributing to the Treeware forest you'll be creating employment for local families and restoring wildlife habitats.
