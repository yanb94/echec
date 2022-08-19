<?php

namespace App\Controller;

use DateTime;
use App\Entity\Post;
use App\Entity\User;
use App\Service\RelativeDate;
use App\Trait\PaginationTrait;
use App\Form\EditMemberFormType;
use App\Entity\ChangeEmailRequest;
use App\Form\EditPasswordFormType;
use App\Repository\PostRepository;
use Symfony\Component\Mime\Address;
use App\Repository\MessageRepository;
use Symfony\Component\Form\FormError;
use App\Form\ChangeEmailRequestFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

#[Route('/space-member')]
class SpaceMemberController extends AbstractController
{
    use PaginationTrait;

    #[Route('', name: 'space_member')]
    public function index(): Response
    {

        $items = [
            [
                "title"=> "Mes informations",
                "icon"=> "fas fa-user",
                "link"=> $this->generateUrl("edit_member")
            ],
            [
                "title"=> "Changer de mot de passe",
                "icon"=> "fas fa-lock",
                "link"=> $this->generateUrl("edit_password")
            ],
            [
                "title"=> "Changer d'email",
                "icon"=> "fas fa-at",
                "link"=> $this->generateUrl("change_email")
            ],
            [
                "title"=> "Sujets suivis",
                "icon"=> "fas fa-comments",
                "link"=> $this->generateUrl("list_follow_subject")
            ],
            [
                "title"=> "Mes contributions",
                "icon"=> "fas fa-edit",
                "link"=> $this->generateUrl("my_contribution")
            ]
        ];

        return $this->render('space_member/index.html.twig', [
            "items" => $items
        ]);
    }

    #[Route("/edit-member", name: "edit_member")]
    public function editMember(Request $request, EntityManagerInterface $em)
    {
        $user = $this->getUser();

        $form = $this->createForm(EditMemberFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();
            
            $this->addFlash("success-edit-member", "Modification des données effectués avec succès");

            return $this->redirectToRoute("edit_member");
        }

        return $this->render("space_member/edit_member.html.twig", [
            "form" => $form->createView()
        ]);
    }

    #[Route("/edit-password", name: "edit_password")]
    public function editPassword(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        /** @var User */
        $user = $this->getUser();

        $form = $this->createForm(EditPasswordFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('newPassword')->getData();

            $user->setPassword(
                $passwordEncoder->encodePassword($user, $newPassword)
            );

            $em->persist($user);
            $em->flush();

            $this->addFlash("success-edit-password", "Modification du mot de passe effectué avec succès");

            return $this->redirectToRoute("edit_password");
        }

        return $this->render("space_member/edit_password.html.twig", [
            "form" => $form->createView()
        ]);
    }

    #[Route("/change-email", name:"change_email")]
    public function changeEmail(
        Request $request,
        EntityManagerInterface $em,
        MailerInterface $mailer,
        RelativeDate $relativeDate,
        ParameterBagInterface $params
    ) {
        $user = $this->getUser();
        $changeEmailRequest = new ChangeEmailRequest();

        $form = $this->createForm(ChangeEmailRequestFormType::class, $changeEmailRequest);

        $form->handleRequest($request);

        if ($form->get('email')->getData() == $user->getEmail()) {
            $form
                ->get('email')
                ->get('first')
                ->addError(new FormError("Votre nouvel email doit être différent de votre email actuel"));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $changeEmailRequest->setUser($user);
            $changeEmailRequest->setExpiresAt(new DateTime('+ 1 hour'));
            $changeEmailRequest->setToken(md5(uniqid()));

            $em->persist($changeEmailRequest);
            $em->flush();
            
            $mailer->send(
                (new TemplatedEmail())
                    ->from(new Address($params->get("adminEmail"), 'Le coin des échecs'))
                    ->to($changeEmailRequest->getEmail())
                    ->subject("Finaliser votre changement d'adresse email")
                    ->htmlTemplate('space_member/email_change_email.mjml.twig')
                    ->context([
                        "link" => $this->generateUrl("confirm_change_email", [
                            "token" => $changeEmailRequest->getToken()
                        ], UrlGeneratorInterface::ABSOLUTE_URL),
                        "date" => $relativeDate->turnDateToStringRelative($changeEmailRequest->getExpiresAt())
                    ])
            );

            return $this->redirectToRoute("notif_change_email");
        }

        return $this->render("space_member/change_email.html.twig", [
            "form" => $form->createView()
        ]);
    }

    #[Route("/change-email/notif", name: "notif_change_email")]
    public function notifChangeEmail()
    {
        return $this->render("space_member/notif_change_email.html.twig");
    }

    /**
     * IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     * @Route("/change-email/{token}", name="confirm_change_email")
     */
    public function confirmChangeEmail(ChangeEmailRequest $changeEmailRequest, EntityManagerInterface $em)
    {
        if ($changeEmailRequest->getExpiresAt() <= new DateTime('now')) {
            throw new NotFoundHttpException("Le token a expiré");
        }
        
        $user = $changeEmailRequest->getUser();
        $user->setEmail($changeEmailRequest->getEmail());

        $em->persist($user);
        $em->remove($changeEmailRequest);

        $em->flush();

        return $this->render("space_member/confirm_change_email.html.twig");
    }

    #[Route("/list-follow-subject/{page}", name: "list_follow_subject")]
    public function listFollowSubject(PostRepository $postRepository, int $page = 1): Response
    {
        $pagination = $this->getParameter("paginationForum");

        $user = $this->getUser();

        $posts = $postRepository->getFollowedSubject($user, $pagination, $page);
        $nb = $postRepository->getNbFollowedSubject($user);

        [$isPreviousPage, $previousPageNb, $isNextPage, $nextPageNb] = $this->generatePaginationValues(
            nb: $nb,
            pagination: $pagination,
            page: $page
        );

        return $this->render('space_member/follow_subject.html.twig', [
            "posts" => $posts,
            'isPreviousPage' => $isPreviousPage,
            'previousPageNb' => $previousPageNb,
            'isNextPage' => $isNextPage,
            'nextPageNb' => $nextPageNb,
        ]);
    }


    #[Route("/unfollow-subject/{id}", name:"unfollow_subject")]
    public function followSubject(Post $post, EntityManagerInterface $em, TagAwareCacheInterface $cache): Response
    {
        if ($post->getUsers()->contains($this->getUser())) {
            $post->removeUser($this->getUser());
            $em->persist($post);
            $em->flush();

            $cache->invalidateTags(['postForum_'.$post->getSlug()]);
        }
        return $this->redirectToRoute("list_follow_subject");
    }

    #[Route("/my-contribution/{page}", name: "my_contribution")]
    public function myContribution(
        MessageRepository $messageRepository,
        int $page = 1
    ) {
        $pagination = $this->getParameter("paginationForum");

        $user = $this->getUser();
        $messages = $messageRepository->getMyMessages($user, $pagination, $page);
        $nb = $messageRepository->nbMyMessages($user);

        [$isPreviousPage, $previousPageNb, $isNextPage, $nextPageNb] = $this->generatePaginationValues(
            nb: $nb,
            pagination: $pagination,
            page: $page
        );

        return $this->render("space_member/my_contribution.html.twig", [
            "messages" => $messages,
            'isPreviousPage' => $isPreviousPage,
            'previousPageNb' => $previousPageNb,
            'isNextPage' => $isNextPage,
            'nextPageNb' => $nextPageNb,
        ]);
    }
}
