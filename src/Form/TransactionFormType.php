<?php

namespace App\Form;

use App\Dto\TransactionFormDTO;
use App\Entity\Currency;
use App\Entity\PaymentMethod;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Luhn;

class TransactionFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('currency', ChoiceType::class, [
                'choices' => [
                    'EUR'  => Currency::EUR_ID ,
                ],
                'choice_label' => function ($value, $key) {
                    return $key;
                },
                'choice_value' => function ($value) {
                    return $value;
                },
            ])
            ->add('paymentMethod', ChoiceType::class, [
                'choices' => [
                    'Cash'  => PaymentMethod::CASH_ID,
                    'Card'  => PaymentMethod::CARD_ID,
                ],
                'choice_label' => function ($value, $key) {
                    return $key;
                },
                'choice_value' => function ($value) {
                    return $value;
                },
            ])
            ->add('amount', IntegerType::class, [
                'label' => false,
                "attr" => [
                    "hidden" => true
                ]
            ])
            ->add('isCashPayment', CheckboxType::class,
                [
                    "required" => false,
                    'label' => false,
                    'attr' => [
                        "hidden" => true
                    ]
                ])
            ->add('cardNumber', TextType::class, [
                'constraints' => [
                    new Luhn([
                        'message' => 'The card number is not valid.',
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' => false,
            ])
            ->add('save', SubmitType::class, [
                'attr' => [
                    'hidden' => true,
                ],
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TransactionFormDTO::class,
        ]);
    }
}
