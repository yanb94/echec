<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Message;
use App\Entity\Post;
use App\Tests\Util\EntityCase;
use PHPUnit\Framework\TestCase;

class PostTest extends EntityCase
{
    private function getValidEntity(): Post
    {
        return (new Post())
            ->setTitle("Ceci est un titre")
            ->setStartMsg(
                (new Message())
                    ->setContent("
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                        Mauris maximus nibh nulla, vitae mollis mi commodo rhoncus.
                        Phasellus non aliquet nisl. In tempus scelerisque cursus.
                        Duis vel risus fringilla, commodo leo vel, eleifend mauris.
                    ")
            )
        ;
    }

    public function testNoErrorWhenPostIsValid(): void
    {
        $post = $this->getValidEntity();
        $this->assertHasErrors($post);
    }

    public function testErrorWhenTitleIsBlank(): void
    {
        $post = $this->getValidEntity()
            ->setTitle("")
        ;

        $this->assertHasErrors($post, 2);
    }

    public function testErrorWhenTitleIsTooShort(): void
    {
        $post = $this->getValidEntity()
            ->setTitle("aaa")
        ;

        $this->assertHasErrors($post, 1);
    }

    public function testErrorWhenTitleIsTooLong(): void
    {
        $post = $this->getValidEntity()
            ->setTitle(
                "Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                Praesent dapibus id enim quis scelerisque"
            )
        ;

        $this->assertHasErrors($post, 1);
    }

    public function testErrorWhenStartMsgIsNotValid(): void
    {
        $post = $this->getValidEntity()
            ->getStartMsg()
            ->setContent("aaa")
        ;

        $this->assertHasErrors($post, 1);
    }

    public function testIncrementNbCommentsWorkHasExpected(): void
    {
        $post = $this->getValidEntity();
        $this->assertSame(0, $post->getNbComments());

        $post->incrementNbComments();
        $this->assertSame(1, $post->getNbComments());
    }

    public function testDecrementNbCommentsWorkHasExpected(): void
    {
        $post = $this->getValidEntity();
        $this->assertSame(0, $post->getNbComments());

        $post->incrementNbComments();
        $this->assertSame(1, $post->getNbComments());

        $post->decrementNbComments();
        $this->assertSame(0, $post->getNbComments());

        $post->decrementNbComments();
        $this->assertSame(0, $post->getNbComments());
    }

    public function testManageHasAnswer(): void
    {
        $post = $this->getValidEntity();

        $post->addMessage(
            (new Message())
                ->setContent("Lorem ipsum dolor sit amet, consectetur adipiscing elit.")
                ->setIsAnswer(false)
        )->addMessage((new Message())
            ->setContent("Lorem ipsum dolor sit amet, consectetur adipiscing elit.")
            ->setIsAnswer(false));

        $post->manageHasAnswer();
        $this->assertFalse($post->getHasAnswer());

        $post->addMessage(
            (new Message())
                ->setContent("Lorem ipsum dolor sit amet, consectetur adipiscing elit.")
                ->setIsAnswer(true)
        );

        $post->manageHasAnswer();
        $this->assertTrue($post->getHasAnswer());
    }

    public function testAnswerMessage(): void
    {
        $post = $this->getValidEntity();

        $post->addMessage(
            (new Message())
                ->setContent("Lorem ipsum dolor sit amet, consectetur adipiscing elit.")
                ->setIsAnswer(false)
        )->addMessage((new Message())
            ->setContent("Lorem ipsum dolor sit amet, consectetur adipiscing elit.")
            ->setIsAnswer(false));
        
        $this->assertEmpty($post->getAnswersMessages());

        $post->addMessage(
            (new Message())
                ->setContent("Lorem ipsum dolor sit amet, consectetur adipiscing elit.")
                ->setIsAnswer(true)
        );

        $this->assertCount(1, $post->getAnswersMessages());
    }
}
