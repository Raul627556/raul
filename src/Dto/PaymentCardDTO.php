<?php

namespace App\Dto;

class PaymentCardDTO extends PaymentDTO
{
    /**
     * @var string
     */
    private string $cardNum;

    /**
     * @return string
     */
    public function getCardNum(): string
    {
        return $this->cardNum;
    }

    /**
     * @param string $cardNum
     * @return PaymentCardDTO
     */
    public function setCardNum(string $cardNum): PaymentCardDTO
    {
        $this->cardNum = $cardNum;
        return $this;
    }



}