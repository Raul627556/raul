<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: 'App\Repository\TransactionChangeRepository')]
#[ORM\Table(name: 'transaction_change')]
class TransactionChange
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Transaction', inversedBy: 'changes')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Transaction $transaction;

    #[ORM\Column(type: 'json')]
    private array $changeDetails = [];

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return TransactionChange
     */
    public function setId(int $id): TransactionChange
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return Transaction
     */
    public function getTransaction(): Transaction
    {
        return $this->transaction;
    }

    /**
     * @param Transaction $transaction
     * @return TransactionChange
     */
    public function setTransaction(Transaction $transaction): TransactionChange
    {
        $this->transaction = $transaction;
        return $this;
    }

    /**
     * @return array
     */
    public function getChangeDetails(): array
    {
        return $this->changeDetails;
    }

    /**
     * @param array $changeDetails
     * @return TransactionChange
     */
    public function setChangeDetails(array $changeDetails): TransactionChange
    {
        $this->changeDetails = $changeDetails;
        return $this;
    }


}
