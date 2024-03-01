<?php

namespace App\Form;

use App\Entity\Campus;
use Doctrine\DBAL\Types\BooleanType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,
                ['label' => 'Name',
                'required' => false
            ])
            ->add('startDateMin',
                DateTimeType::class,
                ['html5' => true,
                    'widget' => 'single_text',
                    'label' => 'Start Date Min',
                    'required' => false
                ])
            ->add('startDateMax',
                DateTimeType::class,
                ['html5' => true,
                    'widget' => 'single_text',
                    'label' => 'Start Date Max',
                    'required' => false
                ])
            ->add('campus',
                EntityType::class,
                    ['class' => Campus::class,
                    'choice_label' => 'name',
                    'label' => 'Campus',
                    'required' => false
                    ])
            ->add('planner', CheckboxType::class,
                    ['required' => false,
                    'label' => 'Events i\'m planning',
                    'required' => false
            ])
            ->add('attendant', CheckboxType::class,
                ['required' => false,
                'label' => 'Events i\'m attending',
                'required' => false
                ])
            ->add('pastEvents', CheckboxType::class,
                ['required' => false,
                'label' => 'Past events',
                'required' => false
                ])
            ->add('Search', SubmitType::class, [
                'label' => 'Search'
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
