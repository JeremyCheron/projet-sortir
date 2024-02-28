<?php

namespace App\Form;

use App\Entity\Campus;
use Doctrine\DBAL\Types\BooleanType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,
                ['label' => 'Name'
            ])
            ->add('startDateMin',
                DateTimeType::class,
                ['html5' => true,
                    'widget' => 'single_text',
                    'label' => 'Start Date Min'])
            ->add('startDateMax',
                DateTimeType::class,
                ['html5' => true,
                    'widget' => 'single_text',
                    'label' => 'Start Date Max'])
            ->add('campus',
                EntityType::class,
                    ['class' => Campus::class,
                    'choice_label' => 'name',
                    'label' => 'Campus'])
            ->add('planner', CheckboxType::class,
                    ['required' => false,
                    'label' => 'Events i\'m planning'
            ])
            ->add('attendant', CheckboxType::class,
                ['required' => false,
                    'label' => 'Events i\'m attending'
                ])
            ->add('pastEvents', CheckboxType::class,
                ['required' => false,
                    'label' => 'Past events'
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
