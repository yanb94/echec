<?php

namespace App\EventSubscriber;

use App\Event\ContactMsgSend;
use App\Service\SendContactEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ContactSubscriber implements EventSubscriberInterface
{
    public function __construct(private SendContactEmail $sendContactEmail)
    {
    }

    public function onContactMsgSend(ContactMsgSend $event): void
    {
        $this->sendContactEmail->sendEmailFromContact($event);
    }

    public static function getSubscribedEvents()
    {
        return [
            ContactMsgSend::class => 'onContactMsgSend',
        ];
    }
}
