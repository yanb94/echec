<?php

namespace App\Form;

use App\Entity\Signal;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\DataTransformer\MessageToIdTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class SignalType extends AbstractType
{
    public function __construct(private MessageToIdTransformer $transformer)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('motif', ChoiceType::class, [
                "choices" => Signal::motifList(),
                "label" => "Motif du signalement",
                "label_attr" => [
                    "class" => "post-forum--modal--cont--form--motif--label"
                ],
                "attr" => [
                    "class" => "post-forum--modal--cont--form--motif--select"
                ]
            ])
            ->add('message', HiddenType::class)
        ;

        $builder->get("message")->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Signal::class,
        ]);
    }
}
