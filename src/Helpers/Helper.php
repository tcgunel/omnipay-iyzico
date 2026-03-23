<?php

namespace Omnipay\Iyzico\Helpers;

use Omnipay\Iyzico\Models\ConvertedPayoutModel;
use Omnipay\Iyzico\Models\InstallmentDetailModel;
use Omnipay\Iyzico\Models\InstallmentPriceModel;
use Omnipay\Iyzico\Models\ItemTransactionModel;

class Helper
{
    /**
     * @param $input
     * @param $var
     */
    public static function format_binNumber($input, &$var)
    {
        $var = substr($input, 0, 6);
    }

    /**
     * @param $input
     * @param $var
     */
    public static function format_price($price, &$var)
    {
        $price = number_format($price, 2, '.', '');

        if (!str_contains($price, '.')) {
            $var = $price . '.0';
        }

        $subStrIndex = 0;

        $priceReversed = strrev($price);

        for ($i = 0, $iMax = strlen($priceReversed); $i < $iMax; $i++) {

            if (strcmp($priceReversed[$i], '0') == 0) {
                $subStrIndex = $i + 1;
            } elseif (strcmp($priceReversed[$i], '.') == 0) {
                $priceReversed = '0' . $priceReversed;
                break;
            } else {
                break;
            }

        }

        $var = strrev(substr($priceReversed, $subStrIndex));

    }

    /**
     * @param $input
     * @param $var
     */
    public static function format_paidPrice($price, &$var)
    {
        $price = number_format($price, 2, '.', '');

        if (!str_contains($price, '.')) {
            $var = $price . '.0';
        }

        $subStrIndex = 0;

        $priceReversed = strrev($price);

        for ($i = 0, $iMax = strlen($priceReversed); $i < $iMax; $i++) {

            if (strcmp($priceReversed[$i], '0') == 0) {
                $subStrIndex = $i + 1;
            } elseif (strcmp($priceReversed[$i], '.') == 0) {
                $priceReversed = '0' . $priceReversed;
                break;
            } else {
                break;
            }

        }

        $var = strrev(substr($priceReversed, $subStrIndex));

    }

    /**
     * @param $input
     * @param $var
     */
    public static function format_cardExpireMonth($input, &$var)
    {
        $var = str_pad($input, 2, '0', STR_PAD_LEFT);
    }

    public static function format_commercial($input, &$var)
    {
        $var = filter_var($input, FILTER_VALIDATE_BOOL);
    }

    public static function format_forceCvc($input, &$var)
    {
        $var = filter_var($input, FILTER_VALIDATE_BOOL);
    }

    public static function format_force3ds($input, &$var)
    {
        $var = filter_var($input, FILTER_VALIDATE_BOOL);
    }

    public static function hash(?string $publicKey, string $privateKey, array $appends, string $random_string): string
    {
        //        $append  = array_map(fn($key) => "$key=$appends[$key]", array_keys($appends));
        $append = [];
        $a = '';

        self::buildBackets(null, $appends, $a);

        $a = substr(str_replace(',]', ']', $a), 0, -1);
        $a = str_replace('],[', '], [', $a);

        $hashStr = $publicKey . $random_string . $privateKey . $a;

        return base64_encode(sha1($hashStr, true));
    }

    public static function hashV2(?string $publicKey, string $privateKey, array $appends, string $random_string, string $uri_path): string
    {
        $payload = empty($appends) ? $random_string . $uri_path : $random_string . $uri_path . json_encode($appends, JSON_THROW_ON_ERROR);

        $encypted_data = hash_hmac('sha256', $payload, $privateKey);

        $auth_string = sprintf('apiKey:%s&randomKey:%s&signature:%s', $publicKey, $random_string, $encypted_data);

        return base64_encode($auth_string);
    }

    public static function buildBackets(?string $startKey, array $items, string &$result)
    {

        if (!empty($startKey) && !is_numeric($startKey)) {
            $result .= $startKey . '=';
        }

        $result .= '[';

        foreach (array_keys($items) as $array_key) {

            if (is_array($items[$array_key])) {

                self::buildBackets($array_key, $items[$array_key], $result);

            } else {

                $result .= "$array_key=$items[$array_key],";

            }

        }

        $result .= '],';

    }

    public static function prettyPrint($data)
    {
        echo '<pre>' . print_r($data, true) . '</pre>';
    }

    public static function format_installmentDetails($input, &$var)
    {
        $var = [];

        foreach ($input as $i) {

            $var[] = new InstallmentDetailModel($i);

        }
    }

    public static function format_installmentPrices($input, &$var)
    {
        $var = [];

        foreach ($input as $i) {

            $var[] = new InstallmentPriceModel($i);

        }
    }

    public static function format_itemTransactions($input, &$var)
    {
        $var = [];

        foreach ($input as $i) {

            $var[] = new ItemTransactionModel($i);

        }
    }

    public static function format_convertedPayout($input, &$var)
    {
        $var = new ConvertedPayoutModel($input);
    }

    public static function format_threeDSHtmlContent($input, &$var)
    {
        $var = base64_decode($input);
    }
}
