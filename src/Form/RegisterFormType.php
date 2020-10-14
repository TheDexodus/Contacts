<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('email', EmailType::class)
            ->add('birthday', BirthdayType::class, [
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',
                ]
            )
            ->add('gender', ChoiceType::class,
                [
                    'choice_attr'  => function ($value, $key) {
                        if ($value !== null) {
                            return [];
                        }

                        return ['value' => $key, 'disabled' => true];
                    },
                    'choice_value' => function ($key) {
                        return $key;
                    },
                    'choices'      => ['Select Gender' => null, 'Male' => User::GENDER_MALE, 'Female' => User::GENDER_FEMALE],
                ]
            )
            ->add('password', RepeatedType::class,
                [
                    'type'            => PasswordType::class,
                    'invalid_message' => 'The password fields must match.',
                    'required'        => true,
                    'first_options'   => ['label' => 'Password'],
                    'second_options'  => ['label' => 'Repeat Password'],
                ]
            )
            ->add('submit', SubmitType::class, ['label' => 'Sign up']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
                'data_class' => User::class,
            ]
        );
    }
}
