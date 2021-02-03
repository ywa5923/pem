<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 * @UniqueEntity("title")
 */
class Project
{


    use TimestampableEntity;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=500)
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 2,
     *      max = 500,
     *      minMessage = "The project name must be at least {{ limit }} characters long",
     *      maxMessage = "The project name cannot be longer than {{ limit }} characters"
     * )
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank
     * @Assert\Choice(
     *     choices={"international","national","economic"},
     *     message="Please choose a valid project type"
     * )
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=500,nullable=true)
     * @Assert\Length(
     *      max = 500,
     *      maxMessage = "The project name cannot be longer than {{ limit }} characters"
     * )
     */
    private $contract;

    /**
     * @ORM\Column(type="string", length=500,nullable=true)
     * @Assert\Length(
     *      max = 500,
     *      maxMessage = "The project name cannot be longer than {{ limit }} characters"
     * )
     */
    private $category;

    /**
     * @return mixed
     */
    public function getContract()
    {
        return $this->contract;
    }

    /**
     * @param mixed $contract
     */
    public function setContract($contract): void
    {
        $this->contract = $contract;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category): void
    {
        $this->category = $category;
    }


    /**
     * @ORM\Column(type="string", length=500,nullable=true)
     * @Assert\Length(
     *      max = 500,
     *      maxMessage = "The project name cannot be longer than {{ limit }} characters"
     * )
     */
    private $description;

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @ORM\Column(type="date",nullable=true)
     * @Assert\Date()
     */
    private $beginDate;

    /**
     * @ORM\Column(type="date",nullable=true)
     * @Assert\Date()
     */
    private $endDate;

    /**
     * @return mixed
     */
    public function getBeginDate()
    {
        return $this->beginDate;
    }

    /**
     * @param mixed $beginDate
     */
    public function setBeginDate($beginDate): void
    {
        $this->beginDate = $beginDate;
    }

    /**
     * @return mixed
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param mixed $endDate
     */
    public function setEndDate($endDate): void
    {
        $this->endDate = $endDate;
    }

    /**
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\UserProject",
     *      mappedBy="project",
     *      orphanRemoval=true,
     *      fetch="EXTRA_LAZY",
     *     cascade={"persist", "remove"}
     *     )
     * @Assert\Valid()
     */
    private $projectUsers;

    public function __construct()
    {
        $this->projectUsers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title=null): self
    {
        $this->title = $title;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type=null): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection|UserProject[]
     */
    public function getProjectUsers(): Collection
    {
        return $this->projectUsers;
    }

    public function addProjectUser(UserProject $projectUser): self
    {
        if (!$this->projectUsers->contains($projectUser)) {
            $this->projectUsers[] = $projectUser;
            $projectUser->setProject($this);
        }

        return $this;
    }

    public function removeProjectUser(UserProject $projectUser): self
    {
        if ($this->projectUsers->contains($projectUser)) {
            $this->projectUsers->removeElement($projectUser);
            // set the owning side to null (unless already changed)
            if ($projectUser->getProject() === $this) {
                $projectUser->setProject(null);
            }
        }

        return $this;
    }

}
