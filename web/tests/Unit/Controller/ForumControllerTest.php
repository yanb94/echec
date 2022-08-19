<?php

namespace App\Tests\Unit\Controller;

use ReflectionMethod;
use PHPUnit\Framework\TestCase;
use App\Repository\PostRepository;
use App\Controller\ForumController;
use App\Repository\CategoryRepository;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;

class ForumControllerTest extends TestCase
{
    public function testGeneratePaginationValues(): void
    {
        $method = new ReflectionMethod(ForumController::class, 'generatePaginationValues');
        $method->setAccessible(true);

        /** @var PaginatedFinderInterface */
        $finder = $this->getMockBuilder(PaginatedFinderInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $controller = new ForumController($finder);

        [$isPreviousPage, $previousPageNb, $isNextPage, $nextPageNb] = $method->invokeArgs($controller, [20, 10, 1]);

        $this->assertFalse($isPreviousPage);
        $this->assertSame(0, $previousPageNb);
        $this->assertTrue($isNextPage);
        $this->assertSame(2, $nextPageNb);

        [$isPreviousPage, $previousPageNb, $isNextPage, $nextPageNb] = $method->invokeArgs($controller, [20, 10, 2]);

        $this->assertTrue($isPreviousPage);
        $this->assertSame(1, $previousPageNb);
        $this->assertFalse($isNextPage);
        $this->assertSame(3, $nextPageNb);
    }

    public function testGetPostandNbQueryWhenNoCategory(): void
    {
        $method = new ReflectionMethod(ForumController::class, 'getPostandNbQuery');
        $method->setAccessible(true);

        /** @var PaginatedFinderInterface */
        $finder = $this->getMockBuilder(PaginatedFinderInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        
        $controller = new ForumController($finder);

        $postRepository = $this->getMockBuilder(PostRepository::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $categoryRepository = $this->getMockBuilder(CategoryRepository::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $categoryRepository->expects($this->never())->method('findOneBy');

        $postRepository->expects($this->once())->method('getPaginatedList')->willReturn([]);
        $postRepository->expects($this->once())->method('getNbPost')->willReturn(10);


        [$posts, $nb, $category] = $method->invokeArgs($controller, [
            $postRepository,
            1,
            10,
            null,
            $categoryRepository
        ]);
    }

    public function testGetPostandNbQueryWhenCategory(): void
    {
        $method = new ReflectionMethod(ForumController::class, 'getPostandNbQuery');
        $method->setAccessible(true);

        /** @var PaginatedFinderInterface */
        $finder = $this->getMockBuilder(PaginatedFinderInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        
        $controller = new ForumController($finder);

        $postRepository = $this->getMockBuilder(PostRepository::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $categoryRepository = $this->getMockBuilder(CategoryRepository::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $postRepository->expects($this->once())->method('getPaginatedListByCategory')->willReturn([]);
        $postRepository->expects($this->once())->method('getNbPostByCategory')->willReturn(10);
        $categoryRepository->expects($this->once())->method('findOneBy');

        [$posts, $nb, $category] = $method->invokeArgs($controller, [
            $postRepository,
            1,
            10,
            "cat1",
            $categoryRepository
        ]);
    }
}
