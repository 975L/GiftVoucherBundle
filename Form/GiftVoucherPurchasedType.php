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
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;

/**
 * GiftVoucherPurchased FormType
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2018 975L <contact@975l.com>
 */
class GiftVoucherPurchasedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('offeredTo', TextType::class, array(
                'label' => 'label.offered_to',
                'required' => true,
                'attr' => array(
                    'placeholder' => 'label.offered_to',
                )))
            ->add('offeredBy', TextType::class, array(
                'label' => 'label.offered_by',
                'required' => true,
                'attr' => array(
                    'placeholder' => 'label.offered_by',
                )))
            ->add('message', TextareaType::class, array(
                'label' => 'label.message',
                'required' => true,
                'attr' => array(
                    'placeholder' => 'label.message',
                    'rows' => '7',
                )))
            ->add('sendToEmail', EmailType::class, array(
                'label' => 'label.email',
                'required' => true,
                'attr' => array(
                    'placeholder' => 'label.email',
                )))
            ->add('userIp', TextType::class, array(
                'label' => 'label.ip',
                'translation_domain' => 'services',
                'required' => true,
                'attr' => array(
                    'readonly' => true,
                )))
        ;
        //GDPR
        if ($options['config']['gdpr']) {
            $builder
                ->add('gdpr', CheckboxType::class, array(
                    'label' => 'text.gdpr',
                    'translation_domain' => 'services',
                    'required' => true,
                    'mapped' => false,
                    ))
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'c975L\GiftVoucherBundle\Entity\GiftVoucherPurchased',
            'intention'  => 'giftVoucherPurchasedForm',
            'translation_domain' => 'giftVoucher',
        ));

        $resolver->setRequired('config');
    }
}
