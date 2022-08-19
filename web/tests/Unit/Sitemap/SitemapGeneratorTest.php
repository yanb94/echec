<?php

namespace App\Tests\Unit\Sitemap;

use Exception;
use App\Entity\Post;
use ReflectionClass;
use ReflectionMethod;
use PHPUnit\Framework\TestCase;
use App\Sitemap\SitemapGenerator;
use App\Repository\PostRepository;
use Error;
use PHPUnit\Framework\MockObject\MockObject;

class SitemapGeneratorTest extends TestCase
{
    public function testExtractUrlParamsFormUrl(): void
    {
        $reflectionPost = $this->getReflectionPostClass();
        $sitemapGenerator = $this->getMockSitemapGenerator();
        $method = $this->getPrivateReflectionMethodForTest(SitemapGenerator::class, "extractUrlParamsFormUrl");

        $result = $method->invokeArgs($sitemapGenerator, [$reflectionPost, [['{slug}','{page}']]]);

        $this->assertSame(['slug','page'], $result);
    }

    public function testExtractUrlParamsForUrlWhenPropertyNotExistInObject(): void
    {
        $reflectionPost = $this->getReflectionPostClass();
        $sitemapGenerator = $this->getMockSitemapGenerator();
        $method = $this->getPrivateReflectionMethodForTest(SitemapGenerator::class, "extractUrlParamsFormUrl");

        $this->expectException(Exception::class);

        $method->invokeArgs($sitemapGenerator, [$reflectionPost, [['{slug}','{manteau}','{page}']]]);
    }

    public function testReplaceUrlParamsByValueInUrl(): void
    {
        $post = (new Post())->setSlug("post");

        $sitemapGenerator = $this->getMockSitemapGenerator();

        $method = $this->getPrivateReflectionMethodForTest(SitemapGenerator::class, "replaceUrlParamsByValueInUrl");
        $url = $method->invokeArgs($sitemapGenerator, ["/{slug}/{page}",['slug','page'],$post]);

        $this->assertSame("/post/{page}", $url);
    }

    public function testReplaceUrlParamsByValueInUrlWhenNoPropertyInEntity(): void
    {
        $post = (new Post())->setSlug("post");

        $sitemapGenerator = $this->getMockSitemapGenerator();

        $method = $this->getPrivateReflectionMethodForTest(SitemapGenerator::class, "replaceUrlParamsByValueInUrl");
        
        $this->expectException(Error::class);
        $url = $method->invokeArgs($sitemapGenerator, ["/{slug}/{manteau}/{page}",['slug','manteau','page'],$post]);
    }

    private function getPrivateReflectionMethodForTest(string $class, string $method)
    {
        $method = new ReflectionMethod($class, $method);
        $method->setAccessible(true);

        return $method;
    }

    private function getReflectionPostClass(): ReflectionClass
    {
        return new ReflectionClass(Post::class);
    }

    private function getMockSitemapGenerator(): MockObject
    {
        return $this->getMockBuilder(SitemapGenerator::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }
}
