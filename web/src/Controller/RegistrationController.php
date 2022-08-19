<?php

namespace App\Controller;

use App\Entity\User;
use App\Sitemap\SitemapRoute;
use App\Security\EmailVerifier;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[
        Route('/register', name: 'app_register'),
        SitemapRoute(changeFreq: "monthly", priority: 0.6)
    ]
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        ParameterBagInterface $params
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $user,
                (new TemplatedEmail())
                    ->from(new Address($params->get("adminEmail"), 'Le coin des échecs'))
                    ->to($user->getEmail())
                    ->subject('Confirmer votre inscription')
                    ->htmlTemplate('registration/confirmation_email.mjml.twig')
            );
            // do anything else you need here, like send an email

            $this->addFlash("register_waiting", "Votre demande d'inscription a été prise en compte");
            return $this->redirectToRoute('register_waiting');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, UserRepository $userRepository): Response
    {
        $id = $request->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('register_confirmation', 'Votre compte a bien été validé');

        return $this->redirectToRoute('register_confirmation');
    }

    #[Route('/register/waiting', name: "register_waiting")]
    public function waitingRegister()
    {
        /** @var Session */
        $session = $this->get('session');
        $flash = $session->getFlashBag();

        if (empty($flash->get('register_waiting'))) {
            throw new NotFoundHttpException("Absence de donnée flash");
        }
        return $this->render("registration/waiting.html.twig");
    }

    #[Route('/register/confirmation', name: "register_confirmation")]
    public function confirmationRegister()
    {
        /** @var Session */
        $session = $this->get('session');
        $flash = $session->getFlashBag();

        if (empty($flash->get('register_confirmation'))) {
            throw new NotFoundHttpException("Absence de donnée flash");
        }
        return $this->render("registration/confirmation.html.twig");
    }
}
