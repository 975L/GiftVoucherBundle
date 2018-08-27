<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * GiftVoucherAvailable FormType
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2018 975L <contact@975l.com>
 */
class GiftVoucherAvailableType extends AbstractType
{
    private $container;

    public function __construct(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $disabled = 'delete' === $options['config']['action'] ? true : false;

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
            ->add('amount', NumberType::class, array(
                'label' => 'label.amount',
                'disabled' => $disabled,
                'required' => true,
                'attr' => array(
                    'placeholder' => 'label.amount',
                )))
            ;
        //All currencies with defaultCurrency selected
        if (empty($this->container->getParameter('c975_l_gift_voucher.proposedCurrencies'))) {
            $dataCurrency = 'create' === $options['config']['action'] ? $this->container->getParameter('c975_l_gift_voucher.defaultCurrency') : $options['data']->getCurrency();
            $builder
                ->add('currency', CurrencyType::class, array(
                    'label' => 'label.currency',
                    'disabled' => $disabled,
                    'required' => true,
                    'data' => $dataCurrency,
                    'attr' => array(
                        'placeholder' => 'label.currency',
                    )))
                ;
        //Only proposed currencies
        } else {
            $currencies = array();
            foreach ($this->container->getParameter('c975_l_gift_voucher.proposedCurrencies') as $currency) {
                $currencies[strtoupper($currency)] = $currency;
            }
            //Multiples currencies
            if (count($currencies) > 1) {
                $builder
                    ->add('currency', ChoiceType::class, array(
                        'label' => 'label.currency',
                        'disabled' => $disabled,
                        'required' => true,
                        'choices' => $currencies,
                        'attr' => array(
                            'placeholder' => 'label.currency',
                        )))
                    ;
            //Only one currency (locked)
            } else {
                $builder
                    ->add('currency', TextType::class, array(
                        'label' => 'label.currency',
                        'disabled' => true,
                        'required' => true,
                        'data' => reset($currencies),
                        ))
                    ;
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'c975L\GiftVoucherBundle\Entity\GiftVoucherAvailable',
            'intention'  => 'giftVoucherAvailableForm',
            'translation_domain' => 'giftVoucher',
        ));

        $resolver->setRequired('config');
    }
}
