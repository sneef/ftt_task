<?php

namespace App\Helpers;

/**
 * Класс для форматирования чисел
 */
class NumberHelper
{
    /**
     * Преобразование числа с запятой в число с точкой (floatval)
     * 
     * @return float
     */
    static function numberWithCommaToPoint($numberWithComma)
    {
        $numberWithPoint = str_replace(',', '.', $numberWithComma);
        
        return floatval($numberWithPoint);
    }
}