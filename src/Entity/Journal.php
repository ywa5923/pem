<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\JournalRepository")
 */
class Journal
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(
     *     max=255,
     *     maxMessage="The maximum number of characters is {{limit}}"
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(
     *     max=255,
     *     maxMessage="The maximum number of characters is {{limit}}"
     * )
     */
    private $abbreviatedName;

    /**
     * @ORM\OneToMany(
     *     targetEntity="JournalFactor",
     *     mappedBy="journal",
     *     fetch="EXTRA_LAZY",
     *     orphanRemoval=true,
     *     cascade={"persist","remove"}
     * )
     * @Assert\Valid()
     */
    private $journalFactors;

    public function __construct()
    {
        $this->journalFactors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAbbreviatedName()
    {
        return $this->abbreviatedName;
    }

    /**
     * @param mixed $abbreviatedName
     */
    public function setAbbreviatedName($abbreviatedName): void
    {
        $this->abbreviatedName = $abbreviatedName;
    }


    /**
     * @return Collection|JournalFactor[]
     */
    public function getJournalFactors(): Collection
    {
        return $this->journalFactors;
    }

    public function addJournalFactor(JournalFactor $journalFactor): self
    {
        if (!$this->journalFactors->contains($journalFactor)) {
            $this->journalFactors[] = $journalFactor;
            $journalFactor->setJournal($this);
        }

        return $this;
    }

    public function removeJournalFactor(JournalFactor $journalFactor): self
    {
        if ($this->journalFactors->contains($journalFactor)) {
            $this->journalFactors->removeElement($journalFactor);
            // set the owning side to null (unless already changed)
            if ($journalFactor->getJournal() === $this) {
                $journalFactor->setJournal(null);
            }
        }

        return $this;
    }
}
