<?php

namespace App\Tests\Unit\Trait;

use App\Trait\PaginationTrait;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

class PaginationTraitTest extends TestCase
{
    public function testGeneratePaginationValues():void
    {
        $paginationTrait = $this->getObjectForTrait(PaginationTrait::class);

        $method = new ReflectionMethod(
            get_class($paginationTrait),
            "generatePaginationValues"
        );

        $method->setAccessible(true);

        [$isPreviousPage, $previousPageNb, $isNextPage, $nextPageNb] = $method->invokeArgs($paginationTrait, [20,10,1]);

        $this->assertFalse($isPreviousPage);
        $this->assertSame(0, $previousPageNb);
        $this->assertTrue($isNextPage);
        $this->assertSame(2, $nextPageNb);

        [$isPreviousPage, $previousPageNb, $isNextPage, $nextPageNb] = $method->invokeArgs($paginationTrait, [20,10,2]);

        $this->assertTrue($isPreviousPage);
        $this->assertSame(1, $previousPageNb);
        $this->assertFalse($isNextPage);
        $this->assertSame(3, $nextPageNb);
    }
}
