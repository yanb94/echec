<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Category;
use App\Tests\Util\EntityCase;
use PHPUnit\Framework\TestCase;

class CategoryTest extends EntityCase
{
    private function getValidEntity(): Category
    {
        return (new Category())
            ->setName('Categorie 1')
        ;
    }

    public function testNoErrorWhenCategoryIsValid(): void
    {
        $this->assertHasErrors($this->getValidEntity());
    }

    public function testErrorWhenCategoryBlankName(): void
    {
        $category = $this->getValidEntity()->setName("");

        $this->assertHasErrors($category, 2);
    }

    public function testErrorWhenCategoryNameTooSmall(): void
    {
        $category = $this->getValidEntity()->setName("aaa");

        $this->assertHasErrors($category, 1);
    }

    public function testErrorWhenCategoryNameTooBig(): void
    {
        $category = $this->getValidEntity()->setName("Lorem ipsum dolor sit amet, consectetur vestibulum.");

        $this->assertHasErrors($category, 1);
    }
}
