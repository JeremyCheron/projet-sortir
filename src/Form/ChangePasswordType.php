<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', RepeatedType::class,
                [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'first_options'  => ['label' => 'New Password'],
                'second_options' => ['label' => 'Repeat Password'],
                'attr' => ['autocomplete' => 'off', 'name' => 'password',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Mandatory password',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Minimum of 6 characters.',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                        // Ajout de la regex :
                        new Regex(
                            [
                                'pattern' => '/"^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{6,}$"/',
                                'message' => 'The password must contain at least one lowercase letter, one uppercase letter, one digit, and one special character ( @, $, !, %, *, \#, ?, &). :'
                            ]
                        ) ]
                    ],
                    'constraints' => [new NotBlank()], 'validation_groups' => ['registration']
                ] )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
