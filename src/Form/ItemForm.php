<?php

namespace App\Form;

use App\Entity\Item;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'rows' => 3,
                ],
                'required' => false,
            ])

            ->add('amount', MoneyType::class, [
                'required' => true,
                'currency' => $options['currency'] ?: $options['data']?->getCurrency(),
            ])
            ->add('currency', CurrencyType::class, [
                'required' => true,
                'placeholder' => 'Select a currency',
                'preferred_choices' => ['GBP', 'EUR', 'USD'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Item::class,
            'currency' => null,
        ]);
    }
}
