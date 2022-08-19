<?php

namespace App\Tests\Unit\Form\DataTransformer;

use ReflectionClass;
use App\Entity\Message;
use PHPUnit\Framework\TestCase;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\DataTransformer\MessageToIdTransformer;
use Symfony\Component\Form\Exception\TransformationFailedException;

class MessageToIdTransformerTest extends TestCase
{
    public function testTranformMessageToId(): void
    {
        $message = $this->getMessageWithId(1);

        /** @var EntityManagerInterface */
        $entityManager = $this->getMockEntityManager();

        $transformer = new MessageToIdTransformer($entityManager);

        $id = $transformer->transform($message);
        $this->assertSame("1", $id);

        $idEmpty = $transformer->transform(null);
        $this->assertSame("", $idEmpty);
    }

    public function testReverseTranformIdToMessage(): void
    {
        $message = $this->getMessageWithId(2);
        
        $messageRepository = $this->getMockMessageRepository(2, $message);

        $entityManager = $this->getMockEntityManager();

        $entityManager
            ->expects($this->once())
            ->method("getRepository")
            ->with(Message::class)
            ->willReturn($messageRepository)
        ;

        /** @var EntityManagerInterface */
        $entityManager = $entityManager;

        $transformer = new MessageToIdTransformer($entityManager);

        $messageFinal = $transformer->reverseTransform(2);
        $this->assertSame(2, $messageFinal->getId());

        $messageNull = $transformer->reverseTransform(null);
        $this->assertNull($messageNull);
    }

    public function testReverseTransformIdToMessageWhenMessageNotExist(): void
    {
        $messageRepository = $this->getMockMessageRepository(3, null);
        $entityManager = $this->getMockEntityManager();

        $entityManager
            ->expects($this->once())
            ->method("getRepository")
            ->with(Message::class)
            ->willReturn($messageRepository)
        ;

        /** @var EntityManagerInterface */
        $entityManager = $entityManager;

        $transformer = new MessageToIdTransformer($entityManager);

        $this->expectException(TransformationFailedException::class);
        $transformer->reverseTransform(3);
    }

    private function getMessageWithId(int $id): Message
    {
        $message = new Message();

        $reflection = new ReflectionClass($message);
        
        $reflectionId = $reflection->getProperty("id");
        $reflectionId->setAccessible(true);
        $reflectionId->setValue($message, $id);

        return $message;
    }

    private function getMockEntityManager()
    {
        return $this->getMockBuilder(EntityManagerInterface::class)
        ->disableOriginalConstructor()
        ->getMock();
    }

    private function getMockMessageRepository(int $idWaited, ?Message $message)
    {
        $messageRepository = $this->getMockBuilder(MessageRepository::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $messageRepository
            ->expects($this->once())
            ->method("find")
            ->with($idWaited)
            ->willReturn($message)
        ;

        return $messageRepository;
    }
}
