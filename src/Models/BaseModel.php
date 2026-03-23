<?php

namespace Omnipay\Iyzico\Models;

use Omnipay\Iyzico\Helpers\Helper;

class BaseModel
{
    public function __construct(?array $abstract)
    {
        foreach ($abstract as $key => $arg) {

            $key = str_replace('-', '_', $key);

            if (property_exists($this, $key)) {

                $this->$key = $arg;

            }

        }

        $this->formatFields();
    }

    private function formatFields()
    {
        $fields = [
            'cardExpireMonth',
            'cardExpireYear',
            'threeD',
            'binNumber',
            'echo',
            'gsm',
            'price',
            'paidPrice',
            'commercial',
            'forceCvc',
            'force3ds',
            'installmentDetails',
            'installmentPrices',
            'itemTransactions',
            'convertedPayout',
            'threeDSHtmlContent',
        ];

        foreach ($fields as $field) {

            if (!empty($this->$field)) {

                $func = "format_{$field}";

                Helper::$func($this->$field, $this->$field);

            }

        }
    }
}
