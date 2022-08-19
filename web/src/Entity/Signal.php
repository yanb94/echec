<?php

namespace App\Entity;

use App\Repository\SignalRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;

/**
 * @HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass=SignalRepository::class)
 * @ORM\Table(name="`signal`")
 */
class Signal
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $motif;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Message::class, inversedBy="signals")
     * @ORM\JoinColumn(nullable=false)
     */
    private $message;

    const SPAM = "spam";
    const SEX = "sex";
    const HAINE = "haine";
    const OTHER = "other";
 
    public static function motifList(): array
    {
        return [
            "Spam" => self::SPAM,
            "Contenu sexuel" => self::SEX,
            "Contenu haineux" => self::HAINE,
            "Autre" => self::OTHER
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(string $motif): self
    {
        $this->motif = $motif;

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

    public function getMessage(): ?Message
    {
        return $this->message;
    }

    public function setMessage(?Message $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function incrementMessageNbSignals(): void
    {
        $this->message->incrementMessageNbSignals();
    }

    /**
     * @ORM\PreRemove
     */
    public function decrementMessageNbSignals(): void
    {
        $this->message->decrementMessageNbSignals();
    }

    /**
     * @ORM\PrePersist
     */
    public function incrementMessageNbSignalsByType(): void
    {
        $arrayNbSignal = $this->message->getNbSignalsByType();

        isset($arrayNbSignal[$this->motif]) ? $arrayNbSignal[$this->motif]++ : $arrayNbSignal[$this->motif] = 1;

        $this->message->setNbSignalsByType($arrayNbSignal);
    }

    /**
     * @ORM\PreRemove
     */
    public function decrementMessageNbSignalsByType(): void
    {
        $arrayNbSignal = $this->message->getNbSignalsByType();

        if (isset($arrayNbSignal[$this->motif])) {
            if ($arrayNbSignal[$this->motif] > 1) {
                $arrayNbSignal[$this->motif]--;
            } else {
                unset($arrayNbSignal[$this->motif]);
            }
        }

        $this->message->setNbSignalsByType($arrayNbSignal);
    }
}
