<?php

namespace App\Integration;

use DateTime;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

/**
 * Интеграция курсов валют
 */
class Currency
{
    /**
     * Получание валюты из источника cbr.ru
     * 
     * @param DateTime $dateCurrency        - объект даты для поиска
     * @param string $currencyCode          - 
     * 
     * @return SimpleXMLElement|false
     */
    static function cbrGet(DateTime $dateCurrency = new DateTime('now'), $currencyCode = '', $baseCurrencyCode = 'RUR')
    {
        $listValutes = Config::get('currency.listValutes');
        $datePrevious = clone $dateCurrency;
        $datePrevious->modify('-1 day');

        $requestParams = [
            'date_req1' => $dateCurrency->format('d/m/Y'),
            'date_req2' => $dateCurrency->format('d/m/Y'),
            'VAL_NM_RQ' => array_key_exists($currencyCode, $listValutes) ? $listValutes[$currencyCode] : ''
        ];

        $targetCurrencyResponse = Http::connectTimeout(2)->get(Config::get('currency.cbrUrl'), $requestParams);

        if ($targetCurrencyResponse->successfull()) {
            $xmlTargetCurrency = simplexml_load_string($targetCurrencyResponse->body());

            return $xmlCurrencies;
        }

        return false;
    }
}