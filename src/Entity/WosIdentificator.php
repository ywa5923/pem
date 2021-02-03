<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WosIdentificatorRepository")
 * @UniqueEntity(fields={"identificator"})
 */
class WosIdentificator
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotNull()
     * @Assert\Length(
     *     max=100,
     *     maxMessage="The maximum number of characters is {{limit}}"
     * )
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\Length(
     *     max=200,
     *     maxMessage="The maximum number of characters is {{limit}}"
     * )
     */
    private $identificator;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="identificators")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type=null): self
    {
        $this->type = $type;

        return $this;
    }

    public function getIdentificator(): ?string
    {
        return $this->identificator;
    }

    public function setIdentificator(?string $identificator=null): self
    {
        $this->identificator = $identificator;

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

    public function __toString()
    {
        return $this->identificator;
    }
}
