<?php
namespace App\Service;

namespace App\Service;

use App\Dto\PaymentCashDTO;

class ChangeCalculatorService
{
    private array $denominations = [
        50000, // 500 euros
        20000, // 200 euros
        10000, // 100 euros
        5000,  // 50 euros
        2000,  // 20 euros
        1000,  // 10 euros
        500,   // 5 euros
        200,   // 2 euros
        100,   // 1 euro
        50,    // 50 céntimos
        20,    // 20 céntimos
        10,    // 10 céntimos
        5,     // 5 céntimos
        2,     // 2 céntimos
        1      // 1 céntimo
    ];


    /**
     * @param int $amount
     * @return array
     */
    public function getChangeCoins(int $amount): array
    {
        $result = [];

        foreach ($this->denominations as $denomination) {
            if ($amount >= $denomination) {
                $count = intdiv($amount, $denomination);
                $result[$denomination] = $count;
                $amount -= $denomination * $count;
            }
        }

        return array_reverse($result, true);
    }


    /**
     * @param $amountToPay
     * @param $coinTypes
     * @return int
     */
    public function changeAmount($amountToPay, $coinTypes): int
    {
        $totalInserted = 0;

        foreach ($coinTypes as $denomination => $count) {
            $totalInserted += $denomination * $count;
        }

        return $totalInserted - $amountToPay;
    }
}
