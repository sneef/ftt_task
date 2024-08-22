<?php

namespace App\Jobs;

use App\Integration\Currency;

use DateTime;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GetCurrencyCbr implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected DateTime $dateCurrency = new DateTime('now'), protected $currencyCode = 'USD', protected $baseCurrencyCode = 'RUR')
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //Собственно получение курса валют из CBR.ru и сохранение результата в файл:

        $currencies = Currency::cbrGet($this->dateCurrency, $this->currencyCode, $this->baseCurrencyCode);

        if ($currencies) {
            info($currencies);
        } else {
            info('FAILED');
        }
    }
}
