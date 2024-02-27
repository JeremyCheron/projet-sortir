<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\City;
use App\Entity\Event;
use App\Entity\EventStatus;
use App\Entity\Place;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
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
        $builder
            ->add('name',TextType::class)
            ->add('startDate', DateTimeType::class,['html5'=>true, 'widget'=>'single_text'])
            ->add('registrationDeadline', DateTimeType::class, ['html5'=>true, 'widget'=>'single_text'])
            ->add('maxRegistrations',IntegerType::class)
            ->add('duration', ChoiceType::class,['choices'=>$duration])
            ->add('description', TextareaType::class)
            ->add('place', EntityType::class, [
                'class' => Place::class,
                'choice_label' => 'name',
            ])
//            ->add('street', EntityType::class,['class'=>Place::class,'choice_label'=>'Street'])
//            ->add('zipCode', EntityType::class,['class'=>City::class,'label'=>'Zip Code'])
//            ->add('latitude', EntityType::class,['class'=>Place::class,'label'=>'Latitude'])
//            ->add('latitude', EntityType::class,['class'=>Place::class,'label'=>'Longitude'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
