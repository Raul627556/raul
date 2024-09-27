<?php

namespace App\Dto;

class PaymentDTO
{

    /** @var int */
    private int $amount;

    /** @var string */
    private string $currency;

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     * @return PaymentDTO
     */
    public function setAmount(int $amount): PaymentDTO
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return PaymentDTO
     */
    public function setCurrency(string $currency): PaymentDTO
    {
        $this->currency = $currency;
        return $this;
    }


}