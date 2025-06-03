<?php

namespace App\Form;

use App\Entity\Purchase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('store', StoreForm::class, [
                'label' => false,
            ])

            ->add('order_id', TextType::class, [
                'required' => true,
                'label' => 'Order Number',
            ])
            ->add('order_date', DateTimeType::class, [
                'required' => true,
                'label' => 'Order Date',
                'html5' => true,
                'widget' => 'single_text',
            ])
            ->add('status', ChoiceType::class, [
                'required' => true,
                'choices' => [
                    'Acknowledged' => Purchase::STATUS_ACKNOWLEDGED,
                    'Dispatched' => Purchase::STATUS_DISPATCHED,
                    'Delivered' => Purchase::STATUS_DELIVERED,
                    'Cancelled' => Purchase::STATUS_CANCELLED,
                ],
                'expanded' => false,
                'multiple' => false,
            ])
            ->add('amount', MoneyType::class, [
                'required' => true,
                'currency' => $options['data']?->getCurrency(),
            ])
            ->add('currency', CurrencyType::class, [
                'required' => true,
                'placeholder' => 'Select a currency',
                'preferred_choices' => ['GBP', 'EUR', 'USD'],
            ])

            ->add('items', CollectionType::class, [
                'entry_type' => ItemForm::class,
                'entry_options' => [
                    'label' => false,
                    'currency' => $options['data']->getCurrency() ?: 'GBP',
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
            ])

            ->add('tracking_courier', TextType::class, [
                'required' => false,
                'label' => 'Courier',
            ])
            ->add('tracking_url', UrlType::class, [
                'required' => false,
                'label' => 'URL',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Purchase::class,
        ]);
    }
}
