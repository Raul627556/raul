<?php

namespace App\Controller;

use App\Dto\TransactionFormDTO;
use App\Entity\PaymentMethod;
use App\Entity\Transaction;
use App\Entity\TransactionChange;
use App\Form\TransactionFormType;
use App\Repository\CurrencyRepository;
use App\Repository\PaymentMethodRepository;
use App\Service\ChangeCalculatorService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PaymentFormController extends AbstractController
{
    private ChangeCalculatorService $changeCalculatorService;
    private EntityManagerInterface $entityManager;
    private PaymentMethodRepository $paymentMethodRepository;
    private CurrencyRepository $currencyRepository;

    public function __construct(
        ChangeCalculatorService $changeCalculatorService,
        EntityManagerInterface  $entityManager,
        PaymentMethodRepository $paymentMethodRepository,
        CurrencyRepository      $currencyRepository
    )
    {
        $this->changeCalculatorService = $changeCalculatorService;
        $this->entityManager = $entityManager;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->currencyRepository = $currencyRepository;
    }

    #[Route('/payment/checkout', name: 'app_payment_checkout', methods: ['POST', 'GET'])]
    public function paymentForm(
        Request                 $request,
        EntityManagerInterface  $entityManager,
        CurrencyRepository      $currencyRepository,
        PaymentMethodRepository $paymentMethodRepository,
        ChangeCalculatorService $changeCalculatorService
    ): Response
    {
        $transactionFormDTO = new TransactionFormDTO();

        $form = $this->createForm(TransactionFormType::class, $transactionFormDTO, [
            'action' => $this->generateUrl('app_payment_checkout'),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $transactionFormDTO = $form->getData();
            $currency = $currencyRepository->find($transactionFormDTO->getCurrency());

            if ($transactionFormDTO->isCashPayment()) {
                $paymentMethod = $paymentMethodRepository->find(PaymentMethod::CARD_ID);
                $changeAmount = $changeCalculatorService->changeAmount($transactionFormDTO->getAmount(), ($request->get("coinType")));

                $transaction = (new Transaction())
                    ->setAmount($transactionFormDTO->getAmount())
                    ->setCurrency($currency)
                    ->setPaymentMethod($paymentMethod)
                    ->setCreatedAt(new DateTime());

                if ($changeAmount < 0) {
                    $transaction->setStatus(Transaction::STATUS_CANCELED);
                    $entityManager->persist($transaction);
                    $entityManager->flush();

                    $this->addFlash('error', 'There is not enough money to pay.');

                    return $this->render('payment/index.html.twig', [
                        'form' => $form->createView(),
                    ]);
                }

                $coinsChange = $changeCalculatorService->getChangeCoins($changeAmount);

                $transaction->setStatus(Transaction::STATUS_COMPLETED);
                $entityManager->persist($transaction);
                $entityManager->flush();

                $transactionChange = new TransactionChange();
                $transactionChange->setTransaction($transaction);
                $transactionChange->setChangeDetails($coinsChange);

                $entityManager->persist($transactionChange);
                $entityManager->flush();

                $changeDetails = [];

                foreach ($coinsChange as $value => $quantity) {
                    $valueInEuros = $value / 100;

                    if ($value >= 500) {
                        $stringCoinType = ' bill of ';
                        if ($quantity > 1) {
                            $stringCoinType = ' bills of ';
                        }
                    } else {
                        $stringCoinType = ' coin of ';
                        if ($quantity > 1) {
                            $stringCoinType = ' coins of ';
                        }
                    }

                    $changeDetails[] = $quantity . $stringCoinType . $valueInEuros . '€' . "\n";
                }

                $changeDetailsString = implode(', ', $changeDetails);

                $this->addFlash('success', 'Transaction completed successfully! The change will be: ' . $changeDetailsString);
            } else {
                $paymentMethod = $paymentMethodRepository->find(PaymentMethod::CARD_ID);

                $transaction = new Transaction();
                $transaction->setAmount($transactionFormDTO->getAmount())
                    ->setCurrency($currency)
                    ->setPaymentMethod($paymentMethod)
                    ->setCreatedAt(new DateTime());

                $transaction->setStatus(Transaction::STATUS_COMPLETED);
                $entityManager->persist($transaction);
                $entityManager->flush();

                $this->addFlash('success', 'Transaction completed successfully! The card payment has been processed.');
            }
        }

        return $this->render('payment/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
