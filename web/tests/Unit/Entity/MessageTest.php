<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Post;
use App\Entity\Message;
use App\Tests\Util\EntityCase;

class MessageTest extends EntityCase
{
    private function getValidEntity(): Message
    {
        return (new Message())
            ->setContent(
                "Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                Mauris maximus nibh nulla, vitae mollis mi commodo rhoncus.
                Phasellus non aliquet nisl. In tempus scelerisque cursus.
                Duis vel risus fringilla, commodo leo vel, eleifend mauris."
            )
        ;
    }

    public function testNoErrorWhenMessageIsvalid(): void
    {
        $msg = $this->getValidEntity();
        $this->assertHasErrors($msg);
    }

    public function testErrorWhenMessageContentIsBlank(): void
    {
        $msg = $this->getValidEntity()
            ->setContent("")
        ;
        $this->assertHasErrors($msg, 2);
    }

    public function testErrorWhenMessageContentIsTooShort(): void
    {
        $msg = $this->getValidEntity()
            ->setContent("aaa")
        ;
        $this->assertHasErrors($msg, 1);
    }

    public function testErrorWhenMessageContentIsTooLong(): void
    {
        $msg = $this->getValidEntity()
            ->setContent(
                "Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                Praesent dapibus id enim quis scelerisque.
                Vestibulum vitae elit luctus, fringilla eros vitae, tempus neque.
                Integer condimentum in lectus nec volutpat.
                Curabitur pulvinar nunc semper lacus bibendum, vel sodales quam fermentum.
                Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.
                Vestibulum venenatis a enim sit amet tristique. Pellentesque viverra ipsum risus.
                Quisque mi ipsum, dictum at pellentesque id, ultricies finibus enim. Suspendisse potenti.
                Curabitur nisl lacus, tempor egestas mauris sit amet, bibendum fringilla justo. 
                Etiam ut leo vel est dictum suscipit.
                Nunc in ex nec mi convallis elementum vitae ac arcu. 
                Curabitur in magna eget erat ullamcorper congue. 
                Quisque ornare orci libero, eu dictum nulla porttitor a. 
                Phasellus nec molestie ligula. 
                Morbi malesuada nisl vel massa dapibus tincidunt eu vel mi. 
                Aliquam nec nibh odio. 
                Etiam dignissim risus nec tempor ultrices. 
                Nunc risus orci gravida."
            )
        ;
        $this->assertHasErrors($msg, 1);
    }

    public function testIncrementPostCommentNbWorkHasExpected(): void
    {
        $msg = $this->getValidEntity();

        $post = (new Post())
            ->setTitle("Post 1")
            ->addMessage($msg)
        ;

        $this->assertSame(0, $post->getNbComments());

        $msg->incrementPostCommentNb();
        $this->assertSame(1, $post->getNbComments());
    }

    public function testDecrementPostCommentNbWorkHasExpected(): void
    {
        $msg = $this->getValidEntity();

        $post = (new Post())
            ->setTitle("Post 1")
            ->addMessage($msg)
        ;

        $this->assertSame(0, $post->getNbComments());

        $msg->incrementPostCommentNb();
        $this->assertSame(1, $post->getNbComments());

        $msg->decrementPostCommentNb();
        $this->assertSame(0, $post->getNbComments());
    }

    public function testSetPostLastMsgDateWorkHasExpectedWhenMessageFromBody(): void
    {
        $msg = $this->getValidEntity();

        $post = (new Post())
            ->setTitle("Post 1")
            ->addMessage($msg)
        ;

        $postLastMsgInitialDate = $post->getLastMsgAt();

        $msg->setPostLastMsgDate();

        $postLastMsgFinalDate = $post->getLastMsgAt();

        $this->assertNotSame($postLastMsgInitialDate, $postLastMsgFinalDate);
    }

    public function testSetPostLastMsgDateWorkHasExpectedWhenMessageFromStart(): void
    {
        $msg = $this->getValidEntity();
        
        $post = (new Post())
            ->setTitle("Post 1")
            ->setStartMsg($msg)
        ;

        $msg->setPost($post);

        $postLastMsgInitialDate = $post->getLastMsgAt();

        $msg->setPostLastMsgDate();

        $postLastMsgFinalDate = $post->getLastMsgAt();

        $this->assertNotSame($postLastMsgInitialDate, $postLastMsgFinalDate);
    }

    public function testIsStartMsg(): void
    {
        $msg = $this->getValidEntity();

        $msg->setPost((new Post())
            ->setStartMsg($msg));

        $this->assertTrue($msg->isStartMsg());

        $msg = $this->getValidEntity();

        $msg->setPostBody((new Post())
            ->addMessage($msg));

        $this->assertFalse($msg->isStartMsg());
    }

    public function testAttachPost(): void
    {
        $msg = $this->getValidEntity();

        $msg->setPost((new Post())
            ->setStartMsg($msg)
            ->setTitle("Titre 1"));

        $this->assertSame("Titre 1", $msg->getAttachPost()->getTitle());

        $msg = $this->getValidEntity();

        $msg->setPostBody((new Post())
            ->addMessage($msg)
            ->setTitle("Titre 2"));

        $this->assertSame("Titre 2", $msg->getAttachPost()->getTitle());
    }
}
