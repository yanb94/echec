<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\Category;
use App\Form\MessageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                "label" => "Titre du post"
            ])
            ->add('categories', EntityType::class, [
                "label" => "Catégories",
                "class" => Category::class,
                "attr" => [
                    "class" => "list-categories"
                ],
                "choice_label" => "name",
                "expanded" => true,
                "multiple" => true
            ])
            ->add('startMsg', MessageType::class, [
                "label" => "Corp du post"
            ])
            ->add('isRequestAnswer', CheckboxType::class, [
                "label" => "Demander une réponse",
                "required" => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
