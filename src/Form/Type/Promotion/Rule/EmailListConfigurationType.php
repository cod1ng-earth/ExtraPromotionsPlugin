<?php

namespace CodingBerlin\ExtraPromotionPlugin\Form\Type\Promotion\Rule;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class EmailListConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('emails', TextareaType::class, [
                'label' => 'sylius.ui.email',
            ])
        ;
    }
}
