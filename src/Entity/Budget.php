<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BudgetRepository")
 */
class Budget
{
    const NONPROFIT='nonprofit';
    const PROFIT='profit';
    /**
     * @ORM\Id()SS
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank()
     */
    private $year;

    /**
     * @ORM\Column(type="decimal", precision=12, scale=3, nullable=true)
     * @Assert\NotBlank()
     */
    private $budget;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(
     *     max=20,
     *     maxMessage="Budget type limit is {{10}} characters"
     *
     * )
     */
    private $type;

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserProject", inversedBy="budgets")
     */
    private $userProject;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getYear(): ?\DateTimeInterface
    {
        return $this->year;
    }

    public function setYear(\DateTimeInterface $year=null): self
    {
        $this->year = $year;

        return $this;
    }

    public function getBudget()
    {
        return $this->budget;
    }

    public function setBudget(?string $budget=null): self
    {
        $this->budget = $budget;

        return $this;
    }

    public function getUserProject(): ?UserProject
    {
        return $this->userProject;
    }

    public function setUserProject(?UserProject $userProject): self
    {
        $this->userProject = $userProject;

        return $this;
    }
}
