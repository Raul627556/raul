<?php

namespace App\Dto;

class PaymentCashDTO extends PaymentDTO
{

    /**
     * @var array
     */
    private array $coinTypes;

    /**
     * @return array
     */
    public function getCoinTypes(): array
    {
        return $this->coinTypes;
    }

    /**
     * @param array $coinTypes
     * @return PaymentCashDTO
     */
    public function setCoinTypes(array $coinTypes): PaymentCashDTO
    {
        $this->coinTypes = $coinTypes;
        return $this;
    }



}