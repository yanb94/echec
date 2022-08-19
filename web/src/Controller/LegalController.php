<?php

namespace App\Controller;

use App\Entity\Legal;
use App\Sitemap\SitemapRoute;
use App\Repository\LegalRepository;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\HttpCacheBundle\Http\SymfonyResponseTagger;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LegalController extends AbstractController
{
    #[
        Route('/legal/{slug}', name: 'legal'),
        SitemapRoute(
            changeFreq: "monthly",
            priority: 0.3,
            repositoryClassQuery: LegalRepository::class,
            entityClass: Legal::class,
            methodQuery: "findAll"
        )
    ]
    public function index(
        string $slug,
        LegalRepository $legalRepository,
        TagAwareCacheInterface $cache,
        SymfonyResponseTagger $responseTagger,
    ): Response {
        $key = "post_".$slug;

        $responseTagger->addTags([$key]);

        $legal = $cache->get($key, function (ItemInterface $item) use ($slug, $legalRepository) {
            return $legalRepository->findOneBy(['slug' => $slug]);
        });

        $response = $this->render('legal/index.html.twig', [
            'legal' => $legal
        ])->setPublic()
            ->setSharedMaxAge(86400)
        ;

        return $response;
    }

    public function partialListLegal(
        LegalRepository $legalRepository,
        string $class,
        TagAwareCacheInterface $cache,
        SymfonyResponseTagger $responseTagger,
    ): Response {

        $responseTagger->addTags(['legal']);

        $legals = $cache->get("list_legals_".$class, function (ItemInterface $item) use ($legalRepository) {
            $item->tag("legal");
            return $legalRepository->findAll();
        });

        $response = $this->render("legal/listLegal.html.twig", [
            "legals" => $legals,
            "class" => $class
        ])->setPublic()
            ->setSharedMaxAge(86400)
        ;

        return $response;
    }
}
