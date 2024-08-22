<?php

namespace App\Integration;

use DateTime;

use App\Helpers\NumberHelper;
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
     * @param string $currencyCode          - код валюты
     * @param string $baseCurrencyCode      - код базовой валюты
     * 
     * @return array|false
     */
    static function cbrGet(DateTime $dateCurrency = new DateTime('now'), $currencyCode = 'USD', $baseCurrencyCode = 'RUR')
    {
        $listValutes = Config::get('currency.listValutes');
        $datePrevious = clone $dateCurrency;
        $datePrevious->modify('-1 day');

        $requestParams = [
            'date_req1' => $datePrevious->format('d/m/Y'),
            'date_req2' => $dateCurrency->format('d/m/Y'),
            'VAL_NM_RQ' => array_key_exists($currencyCode, $listValutes) ? $listValutes[$currencyCode] : ''
        ];

        $targetCurrencyResponse = Http::retry(2, 100)->get(Config::get('currency.cbrUrl'), $requestParams);

        if ($targetCurrencyResponse->successful()) {
            $xmlTargetCurrency = simplexml_load_string($targetCurrencyResponse->body());

            if ($xmlTargetCurrency
                && count($xmlTargetCurrency->Record)
            ) {
                $receivedCurrenciesCount = count($xmlTargetCurrency->Record);

                if ($receivedCurrenciesCount == 2) {
                    $currentCurrency = NumberHelper::numberWithCommaToPoint($xmlTargetCurrency->Record[1]->Value[0]);
                } else {
                    $currentCurrency = 0;
                }
                $previousCurrency = NumberHelper::numberWithCommaToPoint($xmlTargetCurrency->Record[0]->Value[0]);

                $currencyDiff = $currentCurrency - $previousCurrency;
                $currentCurrency = round($currentCurrency, 4);
                $currencyDiff = round($currencyDiff, 4);

                return [
                    'currentCurrency'   => $currentCurrency,
                    'currencyDiff'      => $receivedCurrenciesCount == 2 ? $currencyDiff : 0
                ];
            } else {
                return [
                    'currentCurrency'   => 0,
                    'currencyDiff'      => 0
                ];
            }
        }

        return false;
    }
}