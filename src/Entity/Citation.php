<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CitationRepository")
 */
class Citation
{
    use TimestampableEntity;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date",nullable=false)
     */
    private $year;

    /**
     * @ORM\Column(type="integer")
     */
    private $wosCitations;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $printScreenUrl;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="citations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getYear(): ?\DateTimeInterface
    {
        return $this->year;
    }

    public function setYear(\DateTimeInterface $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getWosCitations(): ?int
    {
        return $this->wosCitations;
    }

    public function setWosCitations(int $wosCitations): self
    {
        $this->wosCitations = $wosCitations;

        return $this;
    }

    public function getPrintScreenUrl(): ?string
    {
        return $this->printScreenUrl;
    }

    public function setPrintScreenUrl(?string $printScreenUrl): self
    {
        $this->printScreenUrl = $printScreenUrl;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
