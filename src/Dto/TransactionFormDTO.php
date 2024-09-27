<?php

namespace App\Dto;

use App\Entity\Currency;

class TransactionFormDTO
{

    /** @var int */
    private $amount;

    /** @var int $currency */
    private $currency;

    /** @var string */
    private $paymentMethod;

    /** @var string */
    private $cardNumber;


    /** @var boolean */
    private $isCashPayment;


    /**
     * @return string
     */
    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }

    /**
     * @param string $paymentMethod
     * @return TransactionFormDTO
     */
    public function setPaymentMethod(string $paymentMethod): TransactionFormDTO
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    /**
     * @return int
     */
    public function getCurrency(): int
    {
        return $this->currency;
    }

    /**
     * @param int $currency
     * @return TransactionFormDTO
     */
    public function setCurrency(int $currency): TransactionFormDTO
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @return string
     */
    public function getCardNumber(): string
    {
        return $this->cardNumber;
    }

    /**
     * @param string $cardNumber
     * @return TransactionFormDTO
     */
    public function setCardNumber(string $cardNumber): TransactionFormDTO
    {
        $this->cardNumber = $cardNumber;
        return $this;
    }

    /**
     * @return bool
     */
    public function isCashPayment(): bool
    {
        return $this->isCashPayment;
    }

    /**
     * @param bool $isCashPayment
     * @return TransactionFormDTO
     */
    public function setIsCashPayment(bool $isCashPayment): TransactionFormDTO
    {
        $this->isCashPayment = $isCashPayment;
        return $this;
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     * @return TransactionFormDTO
     */
    public function setAmount(int $amount): TransactionFormDTO
    {
        $this->amount = $amount;
        return $this;
    }


}