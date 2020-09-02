<?php declare(strict_types=1);

namespace App\Helpers;

class Amount
{
    /**
     * @param mixed $amount
     * @return int
     */
    static public function majorToMinor($amount)
    {
        // @TODO this is not normal for money... add validation, add precision, add overflow check....
        return (int)(number_format((float)$amount, 2) * 100);
    }

    /**
     * @TODO currency exponent
     * @param int $amount
     * @return string
     */
    static public function minorToMajor($amount)
    {
        return number_format($amount / 100, 2, '.', '');
    }
}
