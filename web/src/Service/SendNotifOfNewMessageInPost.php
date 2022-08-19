<?php

namespace App\Service;

use App\Event\AddedMessage;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SendNotifOfNewMessageInPost
{
    public function __construct(
        private MailerInterface $mailer,
        private UrlGeneratorInterface $urlGenerator,
        private string $adminMail
    ) {
    }

    public function sendNotification(AddedMessage $addedMessage)
    {
        $post = $addedMessage->getPost();
        $url = $this->urlGenerator->generate("forum_post", [
            "slug" => $post->getSlug(),
        ], UrlGenerator::ABSOLUTE_URL);

        foreach ($post->getUsers() as $user) {
            $this->mailer->send(
                (new TemplatedEmail())
                    ->from(new Address($this->adminMail, 'Le coin des échecs'))
                    ->to($user->getEmail())
                    ->subject(
                        "Nouveau message a été ajouté sur un post que vous suivez"
                    )
                    ->htmlTemplate('forum/email.mjml.twig')
                    ->context([
                        "post" => $post,
                        "url" => $url
                    ])
            );
        }
    }
}
