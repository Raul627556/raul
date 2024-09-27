<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'payment_methods')]
class PaymentMethod
{
    /** @var int  */
    const CARD_ID = 1;

    /** @var int  */
    const CASH_ID = 2;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return PaymentMethod
     */
    public function setId(int $id): PaymentMethod
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return PaymentMethod
     */
    public function setName(string $name): PaymentMethod
    {
        $this->name = $name;
        return $this;
    }


}
