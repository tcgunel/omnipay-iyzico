<?php

namespace Omnipay\Iyzico\Tests;

use Faker\Factory;
use Omnipay\Iyzico\Gateway;
use Omnipay\Tests\GatewayTestCase;

class TestCase extends GatewayTestCase
{
    public $faker;

    public $public_key = 'sandbox-public';

    public $private_key = 'sandbox-private';

    public $order_id = '6128e5cbf324c6128e5cbf3253';

    public $x_iyzi_rnd = 'TEST_RAND';

    public $x_iyzi_client_version = 'tcgunel/omnipay-iyzico:v0.0.1';

    public function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create('tr_TR');

        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
    }
}
