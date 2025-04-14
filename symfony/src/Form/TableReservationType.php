<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\TableReservation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TableReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('tableNumber')
            ->add('reservationDate', null, [
                'widget' => 'single_text'
            ])
            ->add('client', EntityType::class, [
                'class' => Client::class,
'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TableReservation::class,
        ]);
    }
}
