<?php

namespace App\Entity;

use App\Repository\LegalRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=LegalRepository::class)
 */
class Legal
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
     *      minMessage = "Votre titre doit faire au moins {{ limit }} caractères",
     *      maxMessage = "Votre titre ne doit pas dépasser les {{ limit }} caractères"
     * )
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @Gedmo\Slug(fields={"title"},updatable=false)
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 5,
     *      max = 50,
     *      minMessage = "Votre titre de lien doit faire au moins {{ limit }} caractères",
     *      maxMessage = "Votre titre de lien ne doit pas dépasser les {{ limit }} caractères"
     * )
     * @ORM\Column(type="string", length=50)
     */
    private $titleLink;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 100,
     *      minMessage = "Votre article doit faire au moins {{ limit }} caractères"
     * )
     * @ORM\Column(type="text")
     */
    private $content;

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getTitleLink(): ?string
    {
        return $this->titleLink;
    }

    public function setTitleLink(string $titleLink): self
    {
        $this->titleLink = $titleLink;

        return $this;
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
}
