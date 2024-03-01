<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\City;
use App\Entity\Event;
use App\Entity\EventStatus;
use App\Entity\Place;
use App\Entity\User;
use DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $duration=[];
        for($i=30; $i<=2880; $i+=30){
            $duration[$i]=$i;
        }
        $tomorrow = new DateTime();
        $tomorrow ->modify('+1 day');
        $builder
            ->add('name',TextType::class)
            ->add('startDate', DateTimeType::class,['html5'=>true,
                'widget'=>'single_text',
                'data'=> $tomorrow,
                'attr'=>['min'=>$tomorrow->format('Y-m-d\TH:i')]
            ])
            ->add('registrationDeadline', DateTimeType::class, ['html5'=>true,
                'widget'=>'single_text',
            ])
            ->add('maxRegistrations',IntegerType::class)
            ->add('duration', ChoiceType::class,['choices'=>$duration])
            ->add('description', TextareaType::class)
            ->add('place', EntityType::class, [
                'class' => Place::class,
                'choice_label' => 'name',
            ])
            ->add('save', SubmitType::class,['label'=>'Save'])
            ->add('saveAndPublish', SubmitType::class,['label'=>'Publish the event'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
