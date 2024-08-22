<?php

namespace App\Jobs;

use App\Integration\Currency;

use DateTime;
use Exception;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class GetCurrencyCbr implements ShouldQueue
{
    use Queueable;
    
    const CURRENCY_CACHE_EXPIRATION = 86400;     //секунд, это равно одним суткам

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected DateTime $dateCurrency = new DateTime('now'),
        protected $currencyCode = 'USD',
        protected $baseCurrencyCode = 'RUR'
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $dateKey = $this->dateCurrency instanceof DateTime ? $this->dateCurrency->format('d.m.Y') : '';
        $keyForCache = $dateKey . $this->currencyCode . $this->baseCurrencyCode;
        $cachedCurrency = Cache::get($keyForCache);

        if(! $cachedCurrency) {
            //Собственно получение курса валют из CBR.ru и сохранение результата в файл:
            $currencies = Currency::cbrGet($this->dateCurrency, $this->currencyCode, $this->baseCurrencyCode);
        } else {
            //Подставляем кешированную валюту:
            $currencies = json_decode($cachedCurrency, true);
        }

        if ($currencies) {
            //просто запишем в laravel.log результат
            $currenciesJson = json_encode($currencies);
            info($currenciesJson);
            //И закешируем его:
            Cache::put($keyForCache, $currenciesJson, self::CURRENCY_CACHE_EXPIRATION);
        } else {
            throw new Exception('Failed attemp: ' . $keyForCache);
        }
    }
}
