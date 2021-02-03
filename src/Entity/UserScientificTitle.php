<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserScientificTitleRepository")
 */
class UserScientificTitle
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\Date
     */
    private $beginWith;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\Date
     */
    private $endWith;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotNull()
     * @Assert\Length(
     *     max=100
     * )
     */
    private $grade;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="scientificTitles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBeginWith(): ?\DateTimeInterface
    {
        return $this->beginWith;
    }

    public function setBeginWith(?\DateTimeInterface $beginWith): self
    {
        $this->beginWith = $beginWith;

        return $this;
    }

    public function getEndWith(): ?\DateTimeInterface
    {
        return $this->endWith;
    }

    public function setEndWith(?\DateTimeInterface $endWith): self
    {
        $this->endWith = $endWith;

        return $this;
    }

    public function getGrade(): ?string
    {
        return $this->grade;
    }

    public function setGrade(?string $grade): self
    {
        $this->grade = $grade;

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
