<?php

namespace App\Service;

/** Hay un metodo en symfony que puede hacer esto, pero lo he visto en el último momento */
class LuhnService
{
    /**
     * @param string $cardNumber
     * @return bool
     */
    public function validate(string $cardNumber): bool
    {
        $sum = 0;
        $length = strlen($cardNumber);
        $isSecond = false;

        for ($i = $length - 1; $i >= 0; $i--) {
            $digit = (int) $cardNumber[$i];

            if ($isSecond) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }

            $sum += $digit;
            $isSecond = !$isSecond;
        }

        return $sum % 10 === 0;
    }
}