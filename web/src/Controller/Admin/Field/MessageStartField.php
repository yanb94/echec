<?php

namespace App\Controller\Admin\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

final class MessageStartField implements FieldInterface
{
    use FieldTrait;

    /**
     * @param string|false|null $label
     */
    public static function new(string $propertyName, $label = null): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->addCssClass("admin-field-message-collection--parent")

            ->setTemplatePath('admin/field/message_start.html.twig')
            ->addWebpackEncoreEntries('admin-field-message_collection')

            ->onlyOnDetail()
        ;
    }
}
