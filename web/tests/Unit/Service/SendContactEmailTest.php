<?php

namespace App\Tests\Unit\Service;

use App\Event\ContactMsgSend;
use App\Service\SendContactEmail;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\MailerInterface;

class SendContactEmailTest extends TestCase
{
    public function testEmailIsSend(): void
    {
        $mailer = $this->getMockBuilder(MailerInterface::class)->getMock();

        $mailer->expects($this->once())->method("send");

        /** @var MailerInterface */
        $mailer = $mailer;

        $sendContactEmail = new SendContactEmail($mailer, "example@example.com");

        $sendContactEmail->sendEmailFromContact(new ContactMsgSend(
            firtsname: "John",
            lastname: "Doe",
            email: "example@example",
            message: "Lorem ipsum dolor sit amet, consectetur adipiscing elit."
        ));
    }
}
