<?php

namespace App\Tests\Unit\EventSubscriber;

use App\Entity\Post;
use App\Event\AddedMessage;
use PHPUnit\Framework\TestCase;
use App\Service\SendNotifOfNewMessageInPost;
use App\EventSubscriber\AddedMessageSubscriber;
use Symfony\Component\EventDispatcher\EventDispatcher;

class AddedMessageSubscriberTest extends TestCase
{
    public function testSubscrirerListenTheGoodEvent(): void
    {
        $this->assertArrayHasKey(AddedMessage::class, AddedMessageSubscriber::getSubscribedEvents());
    }

    public function testSubscriberCatchEventHasWaited(): void
    {
        $sendNotifEmail = $this->getMockBuilder(SendNotifOfNewMessageInPost::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $sendNotifEmail->expects($this->once())->method('sendNotification');

        /** @var SendNotifOfNewMessageInPost */
        $sendNotifEmail = $sendNotifEmail;

        $subscriber = new AddedMessageSubscriber($sendNotifEmail);

        $post = (new Post())
            ->setTitle("Post 1")
        ;

        $event = new AddedMessage($post);

        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber($subscriber);
        $dispatcher->dispatch($event);
    }
}
