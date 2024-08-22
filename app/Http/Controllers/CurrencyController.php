<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    /**
     * Метод добавит задачи в очередь
     */
    public function index()
    {
        $currentDate = new DateTime('now');

        for($i=0; $i<160; $i++) {
            \App\Jobs\GetCurrencyCbr::dispatch($currentDate, 'USD', 'RUR');
            $currentDate->modify('-1 day');
        }
    }
}
