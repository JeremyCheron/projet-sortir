<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\City;
use App\Entity\Event;
use App\Entity\EventStatus;
use App\Entity\Place;
use App\Entity\User;
use App\Repository\PlaceRepository;
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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfonycasts\DynamicForms\DependentField;
use Symfonycasts\DynamicForms\DynamicFormBuilder;

class EventType extends AbstractType
{

    public function __construct(private readonly PlaceRepository $placeRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $duration=[];
        for($i=30; $i<=2880; $i+=30){
            $duration[$i]=$i;
        }
        $tomorrow = new DateTime();
        $tomorrow ->modify('+1 day');
        $builder = new DynamicFormBuilder($builder);
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

            ->add('city', EntityType::class, [
                'class' => City::class,
                'choice_label' => 'name',
                'label' => 'City',
                'mapped' => false,
                'placeholder' => 'Select a City'
            ])
            ->add('place', EntityType::class, [
                'class' => Place::class,
                'choice_label' => 'name',
                'placeholder' => 'Select a city first'
            ])

                        ->add('save', SubmitType::class,['label'=>'Save'])
            ->add('saveAndPublish', SubmitType::class,['label'=>'Publish the event'])
        ;

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();

                if (isset($data['city'])) {
                    $city = $data['city'];
                    $places = $this->placeRepository->findBy(['city' => $city]);

                    $choices = [];
                    foreach ($places as $place) {
                        $choices[$place->getName()] = $place;
                    }

                    $form->add('place', EntityType::class, [
                        'class' => Place::class,
                        'label' => "place",
                        'choice_label' => 'name',
                    ]);
                }

            }
        );

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
