<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Signal;
use App\Form\PostType;
use App\Entity\Message;
use App\Form\SignalType;
use App\Form\MessageType;
use App\Event\AddedMessage;
use App\Trait\PaginationTrait;
use App\Repository\PostRepository;
use App\Repository\MessageRepository;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use App\Sitemap\SitemapRoute;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

#[Route('/forum')]
class ForumController extends AbstractController
{
    use PaginationTrait;

    public function __construct(private PaginatedFinderInterface $finder)
    {
    }

    #[
        Route('/{page}', name: 'forum', requirements: ['page' => '\d+']),
        SitemapRoute(
            changeFreq: "hourly",
            priority: 0.9,
            repositoryClassNb: PostRepository::class,
            paginationParam: "paginationForum",
            methodNb: "getNbPost"
        )
    ]
    public function index(
        PostRepository $postRepository,
        CategoryRepository $categoryRepository,
        Request $request,
        TagAwareCacheInterface $cache,
        int $page = 1
    ): Response {
        $pagination = $this->getParameter("paginationForum");
        $categoryParam = $request->query->get('category');
        $searchParam = $request->query->get("s");

        $categories = $cache->get("list_categories", function (ItemInterface $item) use ($categoryRepository) {
            $item->tag(['category']);
            return $categoryRepository->findAll();
        });

        if ($searchParam) {
            $postPaginator = $this->finder->findPaginated($searchParam);
            $postPaginator->setMaxPerPage($pagination);
            $postPaginator->setCurrentPage($page);
            
            $isNextPage = $postPaginator->hasNextPage();
            $nextPageNb = $isNextPage ? $postPaginator->getNextPage() : "";

            $isPreviousPage = $postPaginator->hasPreviousPage();
            $previousPageNb = $isPreviousPage ? $postPaginator->getPreviousPage() : "";

            $searchNb = $postPaginator->getNbResults();
            $posts = $postPaginator->getCurrentPageResults();

            return $this->render('forum/index.html.twig', [
                'posts' => $posts,
                'isPreviousPage' => $isPreviousPage,
                'previousPageNb' => $previousPageNb,
                'isNextPage' => $isNextPage,
                'nextPageNb' => $nextPageNb,
                'searchParam' => $searchParam,
                'category' => null,
                'categories' => $categories,
                'searchNb' => $searchNb,
            ]);
        }

        $keyCache = 'forumList_'.($categoryParam ? : "cat=".$categoryParam."_").$page;

        [$posts, $nb, $category] = $cache->get($keyCache, function (ItemInterface $item) use (
            $postRepository,
            $page,
            $pagination,
            $categoryParam,
            $categoryRepository
        ) {

            $item->tag(['post']);
            return $this->getPostandNbQuery(
                postRepository: $postRepository,
                page: $page,
                pagination: $pagination,
                categoryParam: $categoryParam,
                categoryRepository: $categoryRepository
            );
        });

        [$isPreviousPage, $previousPageNb, $isNextPage, $nextPageNb] = $this->generatePaginationValues(
            nb: $nb,
            pagination: $pagination,
            page: $page
        );

        return $this->render('forum/index.html.twig', [
            'posts' => $posts,
            'isPreviousPage' => $isPreviousPage,
            'previousPageNb' => $previousPageNb,
            'isNextPage' => $isNextPage,
            'nextPageNb' => $nextPageNb,
            'categories' => $categories,
            'searchParam' => $searchParam,
            'category' => $category,
            'searchNb' => null
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/add-post", name="add_post_forum")
     */
    public function addPost(Request $request, EntityManagerInterface $em): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $msg = $post->getStartMsg();

            $msg->setPost($post);

            $em->persist($msg);
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('forum');
        }

        return $this->render('forum/add_post.html.twig', [
            "form" => $form->createView()
        ]);
    }

    #[
        Route("/post/{slug}/{page}", name: "forum_post", requirements: ['page' => '\d+']),
        SitemapRoute(
            changeFreq: "hourly",
            priority: 0.9,
            repositoryClassQuery: PostRepository::class,
            repositoryClassNb: MessageRepository::class,
            entityClass: Post::class,
            methodQuery: "getAllPost",
            methodNb: "getMessageOfAPostNb",
            paginationParam: "paginationPost"
        )
    ]
    public function postForum(
        string $slug,
        PostRepository $postRepository,
        MessageRepository $messageRepository,
        UserRepository $userRepository,
        Request $request,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher,
        TagAwareCacheInterface $cache,
        int $page = 1
    ): Response {

        if ($request->isMethod("GET")) {
            [$post, $followers] = $cache->get("postForumSubject_".$slug, function (ItemInterface $item) use ($postRepository, $userRepository, $slug) {
                $item->tag(['postForum_'.$slug]);
                $post = $postRepository->getPostBySlug($slug);
                $followers = $userRepository->getListOfUserWhoFollowedThePost($post);

                return [$post, $followers];
            });
        } elseif ($request->isMethod("POST")) {
            $post = $postRepository->getPostBySlug($slug);
            $followers = $userRepository->getListOfUserWhoFollowedThePost($post);
            $cache->invalidateTags(['postForum_'.$slug]);
        }

        if ($post->getIsModerate()) {
            throw new NotFoundHttpException("Ce sujet a été modéré");
        }

        $msg = new Message();
        $signal = new Signal();

        $form = $this->createForm(MessageType::class, $msg);
        $formSignal = $this->createForm(SignalType::class, $signal);

        $formSignal->handleRequest($request);

        if ($formSignal->isSubmitted() && $formSignal->isValid()) {
            $em->persist($signal);
            $em->flush();

            $this->addFlash("success_signal", "Votre signalement a bien été pris en compte");

            return $this->redirectToRoute("forum_post", [
                "slug" => $post->getSlug()
            ]);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $msg->setPostBody($post);
            
            $em->persist($msg);
            $em->flush();

            $dispatcher->dispatch(new AddedMessage($post));

            return $this->redirectToRoute("forum_post", [
                "slug" => $post->getSlug()
            ]);
        }

        $pagination = $this->getParameter("paginationPost");

        $keyCache = "postForum_".$post->getSlug()."&page=".$page;

        [$messages, $messagesAnswer, $nb, $nbAnswer] = $cache->get(
            $keyCache,
            function (ItemInterface $item) use ($messageRepository, $post, $page, $pagination) {
                $messages = $messageRepository->getMessageOfAPostPaginated(
                    post: $post,
                    pagination: $pagination,
                    page: $page
                );
        
                $messagesAnswer = $messageRepository->getAnswerMessage($post);
        
                $nb = $messageRepository->getMessageOfAPostNb($post);
                $nbAnswer = $messageRepository->getAnswerMessageNb($post);

                $item->tag(['postForum_'.$post->getSlug()]);

                return [$messages, $messagesAnswer, $nb, $nbAnswer];
            }
        );

        [$isPreviousPage, $previousPageNb, $isNextPage, $nextPageNb] = $this->generatePaginationValues(
            nb: $nb,
            pagination: $pagination,
            page: $page
        );

        return $this->render("forum/subject.html.twig", [
            "post" => $post,
            "messages" => $messages,
            "messagesAnswer" => $messagesAnswer,
            "nbAnswer" => $nbAnswer,
            "form" => $form->createView(),
            'isPreviousPage' => $isPreviousPage,
            'previousPageNb' => $previousPageNb,
            'isNextPage' => $isNextPage,
            'nextPageNb' => $nextPageNb,
            'formSignal' => $formSignal->createView(),
            'nb' => $nb,
            'followers' => $followers
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/answer-sbuject/{id}", name="answer_subject")
     */
    public function answerSubject(Message $message, EntityManagerInterface $em, TagAwareCacheInterface $cache): Response
    {
        if ($message->getIsModerate()) {
            throw new NotFoundHttpException("Ce message a été modéré");
        }
        
        if ($message->getPostBody() == null) {
            throw new NotFoundResourceException();
        }

        if (!$message->getPostBody()->getIsRequestAnswer()) {
            throw new NotFoundResourceException();
        }

        if ($message->getPostBody()->getIsModerate()) {
            throw new NotFoundHttpException("Ce sujet a été modéré");
        }

        if (is_null($this->getUser()) || $message->getPostBody()->getAuthor() != $this->getUser()) {
            throw new AccessDeniedException();
        }

        $message->setIsAnswer(!$message->getIsAnswer());
        $message->getPostBody()->manageHasAnswer();
        
        $em->persist($message);
        $em->flush();

        $cache->invalidateTags(['postForum_'.$message->getPostBody()->getSlug()]);

        return $this->redirectToRoute("forum_post", [
            "slug" => $message->getPostBody()->getSlug()
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/follow-subject/{id}", name="follow_subject")
     */
    public function followSubject(Post $post, EntityManagerInterface $em, TagAwareCacheInterface $cache): Response
    {
        if ($post->getIsModerate()) {
            throw new NotFoundHttpException("Ce sujet a été modéré");
        }

        if ($post->getUsers()->contains($this->getUser())) {
            $post->removeUser($this->getUser());
        } else {
            $post->addUser($this->getUser());
        }

        $em->persist($post);
        $em->flush();

        $cache->invalidateTags(['postForum_'.$post->getSlug()]);

        return $this->redirectToRoute("forum_post", [
            "slug" => $post->getSlug()
        ]);
    }

    private function getPostandNbQuery(
        PostRepository $postRepository,
        int $page,
        int $pagination,
        ?string $categoryParam,
        CategoryRepository $categoryRepository
    ): array {
        $category = null;

        if (!is_null($categoryParam) && $categoryParam != "") {
            $posts = $postRepository->getPaginatedListByCategory(
                pagination: $pagination,
                page: $page,
                idCategory: (int)$categoryParam
            );
            $nb = $postRepository->getNbPostByCategory(idCategory: (int)$categoryParam);
            $category = $categoryRepository->findOneBy(["id" => (int)$categoryParam]);
        } else {
            $posts = $postRepository->getPaginatedList(pagination: $pagination, page: $page);
            $nb = $postRepository->getNbPost();
        }

        return [$posts, $nb, $category];
    }
}
