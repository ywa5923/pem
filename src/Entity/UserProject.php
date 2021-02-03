<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Entity\Budget;
/**
 * @ORM\Entity(repositoryClass="App\Repository\UserProjectRepository")
 * @UniqueEntity(
 *     fields={"user","project"},
 *     errorPath="user",
 *     message="This user was already attached to this project"
 *
 * )
 */
class UserProject
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\Choice(
     *     choices={"responsabil","director"},
     *     message="Your type is not valid"
     * )
     */
    private $type;



    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="userProjects")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull()
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="projectUsers",cascade={"persist","remove"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull()
     */
    private $project;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Budget",
     *     mappedBy="userProject",
     *     orphanRemoval=true,
     *     cascade={"persist", "remove"}
     *     )
     * @Assert\Valid()
     */
    private $budgets;

    public function __construct()
    {
        $this->budgets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user=null): self
    {
        $this->user = $user;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project=null): self
    {
        $this->project = $project;

        return $this;
    }

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
    public function setType(string $type=null): void
    {
        $this->type = $type;
    }


    /**
     * @return Collection|Budget[]
     */
    public function getBudgets(): Collection
    {
        return $this->budgets;
    }

    public function addBudget(Budget $budget): self
    {
        if (!$this->budgets->contains($budget)) {
            $this->budgets[] = $budget;
            $budget->setUserProject($this);

        }

        return $this;
    }

    public function removeBudget(Budget $budget): self
    {
        if ($this->budgets->contains($budget)) {
            $this->budgets->removeElement($budget);
            // set the owning side to null (unless already changed)
            if ($budget->getUserProject() === $this) {
                $budget->setUserProject(null);
            }
        }

        return $this;
    }
}
