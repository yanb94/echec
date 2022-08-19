<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Event\ContactMsgSend;
use App\Sitemap\SitemapRoute;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContactController extends AbstractController
{
    #[
        Route('/contact', name: 'contact'),
        SitemapRoute(changeFreq: "hourly", priority: 0.5)
    ]
    public function index(Request $request, EventDispatcherInterface $dispatcher): Response
    {
        $form = $this->createForm(ContactType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dispatcher->dispatch(new ContactMsgSend(
                firtsname: $form->get('firstname')->getData(),
                lastname: $form->get('lastname')->getData(),
                email: $form->get('email')->getData(),
                message: $form->get('message')->getData()
            ));

            $this->addFlash("contact_notification", "Message envoyé");

            return $this->redirectToRoute("contact_notification");
        }

        return $this->render('contact/index.html.twig', [
            "form" => $form->createView()
        ]);
    }

    #[Route('/contact/notification', name: 'contact_notification')]
    public function contactNotification(): Response
    {
        /** @var Session */
        $session = $this->get('session');
        $flash = $session->getFlashBag();

        if (empty($flash->get('contact_notification'))) {
            throw new NotFoundHttpException("Absence de donnée flash");
        }

        return $this->render('contact/notif.html.twig');
    }
}
