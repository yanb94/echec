<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass=MessageRepository::class)
 */
class Message
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 10,
     *      max = 1000,
     *      minMessage = "Votre message doit faire au moins {{ limit }} caractères",
     *      maxMessage = "Votre message ne doit pas dépasser les {{ limit }} caractères"
     * )
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="messages", fetch="EAGER")
     * @ORM\JoinColumn(nullable=true)
     */
    private $author;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isAnswer = false;

    /**
     * @ORM\OneToOne(targetEntity=Post::class, mappedBy="startMsg", cascade={"persist", "remove"})
     */
    private $post;

    /**
     * @ORM\ManyToOne(targetEntity=Post::class, inversedBy="messages")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $postBody;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isModerate = false;

    /**
     * @ORM\OneToMany(targetEntity=Signal::class, mappedBy="message")
     */
    private $signals;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbSignals = 0;

    /**
     * @ORM\Column(type="object")
     */
    private $nbSignalsByType;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isChecked = false;

    public function __construct()
    {
        $this->signals = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getIsAnswer(): ?bool
    {
        return $this->isAnswer;
    }

    public function setIsAnswer(bool $isAnswer): self
    {
        $this->isAnswer = $isAnswer;

        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(Post $post): self
    {
        // set the owning side of the relation if necessary
        if ($post->getStartMsg() !== $this) {
            $post->setStartMsg($this);
        }

        $this->post = $post;

        return $this;
    }

    public function isStartMsg(): bool
    {
        return is_null($this->post) ? false : true;
    }

    public function getPostBody(): ?Post
    {
        return $this->postBody;
    }

    public function setPostBody(?Post $postBody): self
    {
        $this->postBody = $postBody;

        return $this;
    }

    public function getAttachPost(): Post
    {
        if (is_null($this->post)) {
            return $this->postBody;
        }

        return $this->post;
    }

    /**
     * @ORM\PrePersist
     */
    public function incrementPostCommentNb(): void
    {
        if (!is_null($this->getPostBody())) {
            $this->getPostBody()->incrementNbComments();
        }
    }

    /**
     * @ORM\PreRemove
     */
    public function decrementPostCommentNb(): void
    {
        if (!is_null($this->getPostBody())) {
            $this->getPostBody()->decrementNbComments();
        }
    }

    /**
     * @ORM\PrePersist
     */
    public function setPostLastMsgDate(): void
    {
        if (!is_null($this->getPost())) {
            $this->getPost()->setLastMsgAt(new DateTime('now'));
        }

        if (!is_null($this->getPostBody())) {
            $this->getPostBody()->setLastMsgAt(new DateTime('now'));
        }
    }

    public function getIsModerate(): ?bool
    {
        return $this->isModerate;
    }

    public function setIsModerate(bool $isModerate): self
    {
        $this->isModerate = $isModerate;

        return $this;
    }

    /**
     * @return Collection|Signal[]
     */
    public function getSignals(): Collection
    {
        return $this->signals;
    }

    public function addSignal(Signal $signal): self
    {
        if (!$this->signals->contains($signal)) {
            $this->signals[] = $signal;
            $signal->setMessage($this);
        }

        return $this;
    }

    public function removeSignal(Signal $signal): self
    {
        if ($this->signals->removeElement($signal)) {
            // set the owning side to null (unless already changed)
            if ($signal->getMessage() === $this) {
                $signal->setMessage(null);
            }
        }

        return $this;
    }

    public function getNbSignals(): ?int
    {
        return $this->nbSignals;
    }

    public function setNbSignals(int $nbSignals): self
    {
        $this->nbSignals = $nbSignals;

        return $this;
    }

    public function getNbSignalsByType()
    {
        return $this->nbSignalsByType;
    }

    public function setNbSignalsByType($nbSignalsByType): self
    {
        $this->nbSignalsByType = $nbSignalsByType;

        return $this;
    }

    public function incrementMessageNbSignals(): void
    {
        $this->nbSignals = $this->nbSignals + 1;
    }

    public function decrementMessageNbSignals(): void
    {
        $this->nbSignals = $this->nbSignals == 0 ? 0 : $this->nbSignals - 1;
    }

    public function getIsChecked(): ?bool
    {
        return $this->isChecked;
    }

    public function setIsChecked(bool $isChecked): self
    {
        $this->isChecked = $isChecked;

        return $this;
    }
}
