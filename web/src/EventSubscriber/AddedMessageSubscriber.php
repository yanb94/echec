<?php

namespace App\EventSubscriber;

use App\Event\AddedMessage;
use App\Service\SendNotifOfNewMessageInPost;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddedMessageSubscriber implements EventSubscriberInterface
{
    public function __construct(private SendNotifOfNewMessageInPost $sendNotif)
    {
    }

    public function onAddedMessage(AddedMessage $event)
    {
        $this->sendNotif->sendNotification($event);
    }

    public static function getSubscribedEvents()
    {
        return [
            AddedMessage::class => 'onAddedMessage',
        ];
    }
}
