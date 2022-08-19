<?php

namespace App\Form\DataTransformer;

use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class MessageToIdTransformer implements DataTransformerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Transforms an object (message) to a string (id).
     *
     * @param  Message|null $message
     */
    public function transform($message): string
    {
        if (null === $message) {
            return '';
        }

        return $message->getId();
    }

    /**
     * Transforms a string (id) to an object (message).
     *
     * @param  string $id
     * @throws TransformationFailedException if object (message) is not found.
     */
    public function reverseTransform($messageId): ?Message
    {
        // no message id? It's optional, so that's ok
        if (!$messageId) {
            return null;
        }

        $message = $this->entityManager
            ->getRepository(Message::class)
            // query for the message with this id
            ->find($messageId)
        ;

        if (null === $message) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'An message with id "%s" does not exist!',
                $messageId
            ));
        }

        return $message;
    }
}
