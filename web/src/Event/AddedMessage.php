<?php

namespace App\Event;

use App\Entity\Post;

class AddedMessage
{
    public function __construct(private Post $post)
    {
    }

    /**
     * Get the value of post
     */
    public function getPost(): Post
    {
        return $this->post;
    }

    /**
     * Set the value of post
     *
     * @return  self
     */
    public function setPost($post): self
    {
        $this->post = $post;

        return $this;
    }
}
