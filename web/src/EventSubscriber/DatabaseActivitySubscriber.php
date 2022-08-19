<?php

namespace App\EventSubscriber;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\Legal;
use App\Entity\Message;
use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use FOS\HttpCacheBundle\CacheManager;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class DatabaseActivitySubscriber implements EventSubscriber
{
    public function __construct(
        private TagAwareCacheInterface $cache,
        private CacheManager $httpCache
    ) {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postRemove,
            Events::preUpdate,
        ];
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->invalidateCache($args);
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        $this->invalidateCache($args);
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Legal) {
            $this->invalidateLegalCacheOnUpdate($args, $entity);
        } elseif ($entity instanceof Post) {
            $this->invalidatePostCacheOnUpdate($args, $entity);
        } elseif ($entity instanceof Message) {
            $this->invalidateMessageCacheOnUpdate($args, $entity);
        } elseif ($entity instanceof Category) {
            $this->invalidateCategoryCache($entity);
        }
    }

    private function invalidateCache(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Legal) {
            $this->invalidateLegalCache($entity);
        } elseif ($entity instanceof Post) {
            $this->invalidatePostCache($entity);
        } elseif ($entity instanceof Message) {
            $this->invalidateMessageCache($entity);
        } elseif ($entity instanceof Category) {
            $this->invalidateCategoryCache($entity);
        }
    }

    private function invalidateLegalCache(Legal $legal):void
    {
        $this->cache->invalidateTags(["legal"]);
        $this->httpCache->invalidateTags(["legal"]);
    }

    private function invalidatePostCache(Post $post):void
    {
        $this->cache->invalidateTags(["post"]);
        $this->httpCache->invalidateTags(["post"]);
    }

    private function invalidateMessageCache(Message $message): void
    {
        $this->cache->invalidateTags(['post']);
        $this->httpCache->invalidateTags(["post"]);
    }

    private function invalidateCategoryCache(Category $category): void
    {
        $this->cache->invalidateTags(['category']);
    }

    private function invalidateLegalCacheOnUpdate(PreUpdateEventArgs $args, Legal $legal)
    {
        if ($args->hasChangedField("titleLink")) {
            $this->invalidateLegalCache($legal);
        }

        $this->cache->delete("post_".$legal->getSlug());
        $this->httpCache->invalidateTags(["post_".$legal->getSlug()]);
    }

    private function invalidatePostCacheOnUpdate(PreUpdateEventArgs $args, Post $post)
    {
        $this->cache->invalidateTags(["post","postForum_".$post->getSlug()]);
        $this->httpCache->invalidateTags(["post", "postForum_".$post->getSlug()]);
    }

    private function invalidateMessageCacheOnUpdate(PreUpdateEventArgs $args, Message $message)
    {
        $this->invalidateMessageCache($message);
        $this->cache->invalidateTags(["postForum_".$message->getAttachPost()->getSlug()]);
    }
}
