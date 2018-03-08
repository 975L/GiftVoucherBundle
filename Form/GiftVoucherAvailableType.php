<?php
/*
 * (c) 2018: 975l <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GiftVoucherAvailableType extends AbstractType
{
    //Builds the form
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $disabled = $options['data']->getAction() == 'delete' ? true : false;

        $builder
            ->add('object', TextType::class, array(
                'label' => 'label.object',
                'disabled' => $disabled,
                'required' => true,
                'attr' => array(
                    'placeholder' => 'label.object',
                )))
            ->add('slug', TextType::class, array(
                'label' => 'label.semantic_url',
                'disabled' => $disabled,
                'required' => true,
                'attr' => array(
                    'placeholder' => 'label.semantic_url',
                )))
            ->add('description', TextareaType::class, array(
                'label' => 'label.description',
                'disabled' => $disabled,
                'required' => true,
                'attr' => array(
                    'placeholder' => 'label.description',
                    'rows' => '7',
                )))
            ->add('valid', DateIntervalType::class, array(
                'label' => 'label.period_validity',
                'disabled' => $disabled,
                'required' => true,
                'input' => 'dateinterval',
                'with_years' => true,
                'with_months' => true,
                'with_days' => true,
                'labels' => array(
                    'years' => 'label.years',
                    'months' => 'label.months',
                    'weeks' => 'label.weeks',
                    ),
                ))
            ->add('amount', MoneyType::class, array(
                'label' => 'label.amount',
                'disabled' => $disabled,
                'required' => true,
                'currency' => 'EUR',
                'divisor' => 100,
                'attr' => array(
                    'placeholder' => 'label.amount',
                )))
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'c975L\GiftVoucherBundle\Entity\GiftVoucherAvailable',
            'intention'  => 'giftVoucherAvailableForm',
            'translation_domain' => 'giftVoucher',
        ));
    }
}
