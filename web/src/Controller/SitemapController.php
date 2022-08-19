<?php

namespace App\Controller;

use App\Sitemap\SitemapGenerator;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\HttpCacheBundle\Http\SymfonyResponseTagger;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SitemapController extends AbstractController
{
    #[Route('/sitemap.xml', name: 'sitemap', format: "xml")]
    public function index(
        SitemapGenerator $sitemapGenerator,
        TagAwareCacheInterface $cache,
        SymfonyResponseTagger $responseTagger,
    ): Response {

        $responseTagger->addTags(['post','legal']);

        $pages = $cache->get("sitemap", function (ItemInterface $item) use ($sitemapGenerator) {
            $item->tag(['post','legal']);
            return $sitemapGenerator->generateSitemapData();
        });

        $response = $this->render('sitemap/index.xml.twig', [
            "pages" => $pages
        ])->setPublic()
            ->setSharedMaxAge(86400)
        ;

        return $response;
    }
}
