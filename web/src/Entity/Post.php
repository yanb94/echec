<?php

namespace App\Entity;

use App\Repository\PostRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 */
class Post
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
     *      min = 5,
     *      max = 50,
     *      minMessage = "Le titre du post doit faire au moins {{ limit }} caractères",
     *      maxMessage = "Le titre du post ne doit pas dépasser les {{ limit }} caractères"
     * )
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="posts", fetch="EAGER")
     */
    private $author;

    /**
     * @ORM\ManyToMany(targetEntity=Category::class, inversedBy="posts")
     */
    private $categories;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isRequestAnswer = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hasAnswer = false;

    /**
     * @Assert\Valid
     * @ORM\OneToOne(targetEntity=Message::class, inversedBy="post", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $startMsg;

    /**
     * @ORM\OneToMany(
     *  targetEntity=Message::class,
     *  mappedBy="postBody",
     *  orphanRemoval=true,
     *  cascade={"persist", "remove"}
     * )
     */
    private $messages;

    /**
     * @Gedmo\Slug(fields={"title"},updatable=false)
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbComments = 0;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastMsgAt;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="postsFollow")
     */
    private $users;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isModerate = false;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->lastMsgAt = new DateTime();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);

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

    public function getIsRequestAnswer(): ?bool
    {
        return $this->isRequestAnswer;
    }

    public function setIsRequestAnswer(bool $isRequestAnswer): self
    {
        $this->isRequestAnswer = $isRequestAnswer;

        return $this;
    }

    public function getHasAnswer(): ?bool
    {
        return $this->hasAnswer;
    }

    public function setHasAnswer(bool $hasAnswer): self
    {
        $this->hasAnswer = $hasAnswer;

        return $this;
    }

    public function getStartMsg(): ?Message
    {
        return $this->startMsg;
    }

    public function setStartMsg(Message $startMsg): self
    {
        $this->startMsg = $startMsg;

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setPostBody($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getPostBody() === $this) {
                $message->setPostBody(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getNbComments(): ?int
    {
        return $this->nbComments;
    }

    public function setNbComments(int $nbComments): self
    {
        $this->nbComments = $nbComments;

        return $this;
    }

    public function incrementNbComments(): void
    {
        $this->nbComments = $this->nbComments + 1;
    }

    public function decrementNbComments(): void
    {
        $this->nbComments = $this->nbComments > 0 ? $this->nbComments - 1 : 0;
    }

    public function getLastMsgAt(): ?\DateTimeInterface
    {
        return $this->lastMsgAt;
    }

    public function setLastMsgAt(\DateTimeInterface $lastMsgAt): self
    {
        $this->lastMsgAt = $lastMsgAt;

        return $this;
    }

    public function manageHasAnswer(): void
    {
        /** @var Message */
        foreach ($this->messages as $msg) {
            if ($msg->getIsAnswer()) {
                $this->setHasAnswer(true);
                return;
            }
        }

        $this->setHasAnswer(false);
    }

    public function getAnswersMessages(): array
    {
        $answerMessages = [];

        foreach ($this->messages as $msg) {
            if ($msg->getIsAnswer()) {
                $answerMessages[] = $msg;
            }
        }

        return $answerMessages;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addPostsFollow($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removePostsFollow($this);
        }

        return $this;
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

    public function enabled(): bool
    {
        return !$this->isModerate;
    }
}
