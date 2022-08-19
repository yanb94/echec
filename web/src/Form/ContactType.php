<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastname', TextType::class, [
                "label" => "Nom",
                "attr" => [
                    "placeholder" => "Indiquer votre nom"
                ],
                "constraints" => [
                    new NotBlank(message: "Vous devez indiquer votre nom"),
                    new Length(
                        min: 2,
                        max: 50,
                        minMessage: "Votre nom doit faire au moins {{ limit }} caractères",
                        maxMessage: "Votre nom ne dois pas dépasser les {{ limit }} caractères"
                    )
                ]
            ])
            ->add('firstname', TextType::class, [
                "label" => "Prénom",
                "attr" => [
                    "placeholder" => "Indiquer votre prénom"
                ],
                "constraints" => [
                    new NotBlank(message: "Vous devez indiquer votre prénom"),
                    new Length(
                        min: 2,
                        max: 50,
                        minMessage: "Votre prénom doit faire au moins {{ limit }} caractères",
                        maxMessage: "Votre prénom ne dois pas dépasser les {{ limit }} caractères"
                    )
                ]
            ])
            ->add('email', EmailType::class, [
                "label" => "Email",
                "attr" => [
                    "placeholder" => "Indiquer votre email"
                ],
                "constraints" => [
                    new NotBlank(message: "Vous devez indiquer votre email"),
                    new Email(message: "Votre email doit être valide")
                ]
            ])
            ->add('message', TextareaType::class, [
                "label" => "Votre message",
                "attr" => [
                    "placeholder" => "Entrez votre message"
                ],
                "constraints" => [
                    new NotBlank(message: "Vous devez indiquer votre message"),
                    new Length(
                        min: 10,
                        max: 1000,
                        minMessage: "Votre message doit faire au moins {{ limit }} caractères",
                        maxMessage: "Votre message ne dois pas dépasser les {{ limit }} caractères"
                    )
                ]
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
