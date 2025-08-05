<?php

namespace App\Form;

use App\Entity\CampaignStand;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class CampaignStandType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('district', TextType::class, [
                'label' => 'Stadtteil/Ort',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'z.B. Innenstadt, Bahnhof, Marktplatz'
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Bitte geben Sie einen Stadtteil oder Ort an.']),
                    new Assert\Length([
                        'min' => 2,
                        'max' => 100,
                        'minMessage' => 'Der Stadtteil muss mindestens {{ limit }} Zeichen lang sein.',
                        'maxMessage' => 'Der Stadtteil darf maximal {{ limit }} Zeichen lang sein.'
                    ])
                ]
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Straße und Hausnummer (optional)'
                ],
                'constraints' => [
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'Die Adresse darf maximal {{ limit }} Zeichen lang sein.'
                    ])
                ]
            ])
            ->add('startTime', DateTimeType::class, [
                'label' => 'Startzeit',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Bitte geben Sie eine Startzeit an.']),
                    new Assert\GreaterThan([
                        'value' => 'now',
                        'message' => 'Die Startzeit muss in der Zukunft liegen.'
                    ])
                ]
            ])
            ->add('endTime', DateTimeType::class, [
                'label' => 'Endzeit',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Bitte geben Sie eine Endzeit an.']),
                    new Assert\GreaterThan([
                        'propertyPath' => 'parent.all[startTime].data',
                        'message' => 'Die Endzeit muss nach der Startzeit liegen.'
                    ])
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Beschreibung',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Zusätzliche Informationen zum Stand (optional)'
                ],
                'constraints' => [
                    new Assert\Length([
                        'max' => 1000,
                        'maxMessage' => 'Die Beschreibung darf maximal {{ limit }} Zeichen lang sein.'
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CampaignStand::class,
        ]);
    }
}
