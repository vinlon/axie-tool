<?php

/**
 * 金额转换：分转换为元.
 *
 * @param int $fen
 * @param bool $withThousandsSeparator
 *
 * @return string
 */
function toYuan($fen, $withThousandsSeparator = false)
{
    $thousandsSep = $withThousandsSeparator ? ',' : '';

    return number_format($fen / 100, 2, '.', $thousandsSep);
}

/**
 * 金额转换：元转换为分.
 *
 * @param string $yuan
 *
 * @return int
 */
function toFen($yuan)
{
    // 替换千分符
    $yuan = str_replace(',', '', $yuan);

    return intval($yuan * 100);
}

function toEth($price, $decimals = 5)
{
    $ethPrice = $price / bcpow(10, 18);
    return number_format($ethPrice, $decimals, '.', null);
}

function toWei($ethPrice)
{
    $weiPrice = $ethPrice * bcpow(10, 18);
    return intval($weiPrice);
}

function format_address($address)
{
    if (\Str::startsWith($address, 'ronin:')) {
        $address = str_replace('ronin:', '0x', $address);
    }
    if (!\Str::startsWith($address, '0x')) {
        $address = '0x' . $address;
    }
    return $address;
}

/**
 * 除法计算(处理分母为0的情况).
 *
 * @param $numerator
 * @param $denominator
 * @param $precision
 * @param int $defaultValue
 *
 * @return float|int
 */
function safe_divide($numerator, $denominator, $precision = null, $defaultValue = 0)
{
    if (0 == $numerator) {
        return 0;
    }
    if (0 == $denominator) {
        return $defaultValue;
    }

    $divideResult = $numerator / $denominator;
    if ($precision) {
        return round($divideResult, $precision);
    }

    return $divideResult;
}

function arr_incr(&$arr, $key, $incrValue = 1)
{
    $value = Arr::get($arr, $key, 0);
    Arr::set($arr, $key, $value + $incrValue);
}
