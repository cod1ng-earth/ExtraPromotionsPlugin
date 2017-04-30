<?php

namespace Acme\ExamplePlugin\Form\Type\Promotion\Action;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class NthItemUnitDiscountConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nth_item_unit_discount', IntegerType::class, [
                'label' => 'sylius.ui.nth',
            ])
        ;
    }
}
