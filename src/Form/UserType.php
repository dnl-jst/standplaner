<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'E-Mail-Adresse',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'benutzer@example.com'
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Bitte geben Sie eine E-Mail-Adresse an.']),
                    new Assert\Email(['message' => 'Bitte geben Sie eine gültige E-Mail-Adresse an.'])
                ]
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Benutzerrollen',
                'choices' => [
                    'Benutzer' => 'ROLE_USER',
                    'Administrator' => 'ROLE_ADMIN',
                ],
                'multiple' => true,
                'expanded' => true,
                'label_attr' => [
                    'class' => 'form-check-label'
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Bitte wählen Sie mindestens eine Rolle aus.'])
                ]
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'required' => !$options['is_edit'],
                'first_options' => [
                    'label' => $options['is_edit'] ? 'Neues Passwort (leer lassen zum Beibehalten)' : 'Passwort',
                    'attr' => [
                        'class' => 'form-control',
                        'autocomplete' => 'new-password'
                    ]
                ],
                'second_options' => [
                    'label' => 'Passwort wiederholen',
                    'attr' => [
                        'class' => 'form-control',
                        'autocomplete' => 'new-password'
                    ]
                ],
                'invalid_message' => 'Die Passwörter müssen übereinstimmen.',
                'constraints' => !$options['is_edit'] ? [
                    new Assert\NotBlank(['message' => 'Bitte geben Sie ein Passwort an.']),
                    new Assert\Length([
                        'min' => 6,
                        'minMessage' => 'Das Passwort muss mindestens {{ limit }} Zeichen lang sein.',
                        'max' => 4096,
                    ])
                ] : [
                    new Assert\Length([
                        'min' => 6,
                        'minMessage' => 'Das Passwort muss mindestens {{ limit }} Zeichen lang sein.',
                        'max' => 4096,
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_edit' => false,
        ]);
    }
}
