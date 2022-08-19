<?php

namespace App\Controller;

use App\Sitemap\SitemapRoute;
use App\Repository\PostRepository;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\HttpCacheBundle\Http\SymfonyResponseTagger;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomepageController extends AbstractController
{
    #[
        Route('/', name: 'homepage'),
        SitemapRoute(changeFreq: "hourly", priority: 1)
    ]
    public function index(): Response
    {
        $response = $this->render('homepage/index.html.twig', [])->setPublic()
            ->setSharedMaxAge(86400)
        ;

        return $response;
    }

    public function secondActionButton(): Response
    {
        $response = $this->render("homepage/second_action_button.html.twig")
            ->setPrivate()
            ->setSharedMaxAge(0)
        ;
        return $response;
    }

    public function listTopPost(
        PostRepository $postRepository,
        TagAwareCacheInterface $cache,
        SymfonyResponseTagger $responseTagger
    ): Response {
        $responseTagger->addTags(['post']);
        $nbPostOnHomepage = $this->getParameter("nbPostOnHomepage");

        $posts = $cache->get("listTopPost", function (ItemInterface $item) use ($postRepository, $nbPostOnHomepage) {
            $item->tag(['post']);
            return $postRepository->getLastSubjectForHomepage($nbPostOnHomepage);
        });

        $response = $this->render("homepage/list_top_post.html.twig", [
            "posts" => $posts
        ])->setPublic()
        ->setSharedMaxAge(86400)
        ;

        return $response;
    }
}
