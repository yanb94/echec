<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class EditPasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("oldPassword", PasswordType::class, [
                "label" => "Mot de passe actuel",
                "mapped" => false,
                "attr" => [
                    "placeholder" => "Entrez votre mot de passe actuel"
                ],
                "constraints" => [
                    new UserPassword(message:"Mot de passe incorrect")
                ]
            ])
            ->add("newPassword", RepeatedType::class, [

                'type' => PasswordType::class,
                'invalid_message' => 'Vos mot de passe doivent être identique',
                'required' => true,
                'mapped' => false,
                'first_options' => [
                    'label' => 'Votre nouveau mot de passe',
                    "attr" => [
                        "placeholder" => "Entrez votre mot de passe"
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Vous devez entrer un mot de passe',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Votre mot de passe doit faire au moins {{ limit }} caractères',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmer votre nouveau mot de passe',
                    "attr" => [
                        "placeholder" => "Confirmer votre mot de passe"
                    ]
                ],


            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
