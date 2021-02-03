<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\JournalDetailRepository")
 */
class JournalFactor
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotNull()
     */
    private $year;

    /**
     * @ORM\Column(type="decimal", precision=7, scale=3)
     * @Assert\NotNull()
     */
    private $AIS;

    /**
     * @ORM\Column(type="decimal", precision=7, scale=3)
     * @Assert\NotBlank()
     */
    private $impactFactor;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Journal", inversedBy="journalFactors")
     * @ORM\JoinColumn(nullable=false)
     */
    private $journal;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getYear(): ?\DateTimeInterface
    {
        return $this->year;
    }

    public function setYear(?\DateTimeInterface $year=null): self
    {
        $this->year = $year;

        return $this;
    }

    public function getAIS()
    {
        return $this->AIS;
    }

    public function setAIS(?float $AIS): self
    {
        $this->AIS = $AIS;

        return $this;
    }

    public function getImpactFactor()
    {
        return $this->impactFactor;
    }

    public function setImpactFactor(?float $impactFactor): self
    {
        $this->impactFactor = $impactFactor;

        return $this;
    }

    public function getJournal(): ?Journal
    {
        return $this->journal;
    }

    public function setJournal(?Journal $journal): self
    {
        $this->journal = $journal;

        return $this;
    }
}
