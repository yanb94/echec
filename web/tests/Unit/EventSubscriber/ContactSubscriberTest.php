<?php

namespace App\Tests\Unit\EventSubscriber;

use App\Event\ContactMsgSend;
use App\EventSubscriber\ContactSubscriber;
use App\Service\SendContactEmail;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\KernelInterface;

class ContactSubscriberTest extends TestCase
{
    public function testSubscrirerListenTheGoodEvent(): void
    {
        $this->assertArrayHasKey(ContactMsgSend::class, ContactSubscriber::getSubscribedEvents());
    }

    public function testSubscriberCatchEventHasWaited(): void
    {
        $sendContactEmail = $this->getMockBuilder(SendContactEmail::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $sendContactEmail->expects($this->once())->method('sendEmailFromContact');

        /** @var SendContactEmail */
        $sendContactEmail = $sendContactEmail;

        $subscriber = new ContactSubscriber($sendContactEmail);

        $event = new ContactMsgSend(
            firtsname: "John",
            lastname: "Doe",
            email: "example@example.com",
            message: "Lorem ipsum dolor sit amet, consectetur adipiscing elit."
        );

        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber($subscriber);
        $dispatcher->dispatch($event);
    }
}
