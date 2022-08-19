<?php

namespace App\Service;

use App\Event\ContactMsgSend;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class SendContactEmail
{
    public function __construct(
        private MailerInterface $mailer,
        private string $adminMail
    ) {
    }

    public function sendEmailFromContact(ContactMsgSend $contact): void
    {
        $this->mailer->send(
            (new TemplatedEmail())
                ->from(new Address($this->adminMail, 'Le coin des échecs'))
                ->to($this->adminMail)
                ->subject(
                    "Un nouveau message de ".$contact->getFirtsname()." ".$contact->getLastname()
                )
                ->htmlTemplate('contact/email.mjml.twig')
                ->context([
                    "firstname" => $contact->getFirtsname(),
                    "lastname" => $contact->getLastname(),
                    "emailAddress" => $contact->getEmail(),
                    "message" => $contact->getMessage()
                ])
        );
    }
}
