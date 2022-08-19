<?php

namespace App\Form;

use App\Entity\ChangeEmailRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ChangeEmailRequestFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', RepeatedType::class, [
                "type" => EmailType::class,
                'first_options' => [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez indiquer un email',
                        ])
                    ],
                    'label' => 'Nouvel email',
                    'attr' => [
                        "placeholder" => "Indiquer un email"
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmer votre nouvel email',
                    'attr' => [
                        "placeholder" => "Confirmer votre nouvel email"
                    ]
                ],
                'invalid_message' => 'Vos email doivent être identique',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ChangeEmailRequest::class
        ]);
    }
}
