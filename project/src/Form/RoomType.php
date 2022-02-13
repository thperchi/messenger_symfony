<?php

namespace App\Form;

use App\Entity\Room;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('users', EntityType::class, [
                'label' => 'Users',
                'class' => User::class,
                'choice_label' => 'email',
                'multiple' => true,
                'expanded' => true,
                'query_builder' => function (UserRepository $ur) {
                    return $ur->findAllExeptLogged();
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Room::class,
        ]);
    }
}
