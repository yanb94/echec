<?php

namespace App\Controller;

use App\Sitemap\SitemapRoute;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\HttpCacheBundle\Http\SymfonyResponseTagger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AboutController extends AbstractController
{
    #[
        Route('/about', name: 'about'),
        SitemapRoute(changeFreq: "monthly", priority: 0.6)
    ]
    public function index(SymfonyResponseTagger $responseTagger): Response
    {
        $responseTagger->addTags(["about"]);

        $response = $this->render('about/index.html.twig', [])
            ->setPublic()
            ->setSharedMaxAge(100)
        ;

        return $response;
    }
}
