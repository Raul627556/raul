<?php

namespace App\Controller;

use App\Dto\PaymentCardDTO;
use App\Dto\PaymentCashDTO;
use App\Entity\PaymentMethod;
use App\Entity\Transaction;
use App\Entity\TransactionChange;
use App\Repository\CurrencyRepository;
use App\Repository\PaymentMethodRepository;
use App\Service\ChangeCalculatorService;
use App\Service\LuhnService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class PaymentController extends AbstractController
{

    /** @var LuhnService $luhnService */
    private LuhnService $luhnService;

    /** @var ChangeCalculatorService $changeCalculatorService */
    private ChangeCalculatorService $changeCalculatorService;

    /** @var EntityManagerInterface $entityManager */
    private EntityManagerInterface $entityManager;

    /** @var PaymentMethodRepository $paymentMethodRepository */
    private PaymentMethodRepository $paymentMethodRepository;

    /** @var CurrencyRepository $currencyRepository */
    private CurrencyRepository $currencyRepository;



    /**
     * @param LuhnService $luhnService
     * @param ChangeCalculatorService $changeCalculatorService
     * @param EntityManagerInterface $entityManager
     * @param PaymentMethodRepository $paymentMethodRepository
     * @param CurrencyRepository $currencyRepository
     */
    public function __construct(
        LuhnService             $luhnService,
        ChangeCalculatorService $changeCalculatorService,
        EntityManagerInterface  $entityManager,
        PaymentMethodRepository $paymentMethodRepository,
        CurrencyRepository      $currencyRepository
    )
    {
        $this->luhnService = $luhnService;
        $this->changeCalculatorService = $changeCalculatorService;
        $this->entityManager = $entityManager;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->currencyRepository = $currencyRepository;
    }

    #[Route('/payment/card', name: 'app_payment_card', methods: ['POST'])]
    public function cardPayment(
        Request                $request,
        SerializerInterface    $serializer,
        EntityManagerInterface $entityManager,
    ): JsonResponse
    {

        $paymentCard = $serializer->deserialize($request->getContent(), PaymentCardDTO::class, 'json');

        $currency = $this->currencyRepository->findOneBy(['code' => $paymentCard->getCurrency()]);
        $paymentMethod = $this->paymentMethodRepository->find(PaymentMethod::CARD_ID);

        $transaction = new Transaction();
        $transaction
            ->setAmount($paymentCard->getAmount())
            ->setCurrency($currency)
            ->setPaymentMethod($paymentMethod)
            ->setCreatedAt(new DateTime());

        if ($this->luhnService->validate($paymentCard->getCardNum())) {

            $transaction->setStatus(Transaction::STATUS_COMPLETED);
            $entityManager->persist($transaction);
            $entityManager->flush();

            return new JsonResponse(['success' => true], 200);

        } else {

            $transaction->setStatus(Transaction::STATUS_CANCELED);
            $entityManager->persist($transaction);
            $entityManager->flush();

            return new JsonResponse(['success' => false, 'error' => 702], 400);
        }
    }

    #[Route('/payment/cash', name: 'app_payment_cash', methods: ['POST'])]
    public function cashPayment(
        Request                $request,
        SerializerInterface    $serializer,
        EntityManagerInterface $entityManager): JsonResponse
    {

        $paymentCash = $serializer->deserialize($request->getContent(), PaymentCashDTO::class, 'json');

        $amountToPay = $paymentCash->getAmount();
        $coinTypes = $paymentCash->getCoinTypes();

        $changeAmount = $this->changeCalculatorService->changeAmount($amountToPay, $coinTypes);

        $currency = $this->currencyRepository->findOneBy(['code' => $paymentCash->getCurrency()]);
        $paymentMethod = $this->paymentMethodRepository->find(PaymentMethod::CASH_ID);

        $transaction = new Transaction();
        $transaction->setAmount($paymentCash->getAmount())
            ->setCurrency($currency)
            ->setPaymentMethod($paymentMethod)
            ->setCreatedAt(new DateTime());

        if ($changeAmount < 0) {
            $transaction->setStatus(Transaction::STATUS_CANCELED);
            $entityManager->persist($transaction);
            $entityManager->flush();
            return new JsonResponse(['success' => false, 'error' => 'There is not enough money to pay'], 400);
        }

        $change = $this->changeCalculatorService->getChangeCoins($changeAmount);

        $transaction->setStatus(Transaction::STATUS_COMPLETED);
        $entityManager->persist($transaction);
        $entityManager->flush();

        $transactionChange = new TransactionChange();
        $transactionChange->setTransaction($transaction);
        $transactionChange->setChangeDetails($change);

        $entityManager->persist($transactionChange);
        $entityManager->flush();

        return new JsonResponse(['success' => true, "amount" => $paymentCash->getAmount(), "coin_types" => $change], 200);
    }


}
