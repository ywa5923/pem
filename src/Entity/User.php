<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Groups("main")
     * @Assert\NotBlank()
     * @Assert\Length(
     *     max=100,
     *     maxMessage="The maximum number of characters is {{limit}}"
     * )
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=50)
     * @Groups("main")
     * @Assert\NotBlank()
     * @Assert\Length(
     *     max=100,
     *     maxMessage="The maximum number of characters is {{limit}}"
     * )
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=50,nullable=true)
     * @Groups("main")
     * @Assert\Length(
     *     max=100,
     *     maxMessage="The maximum number of characters is {{limit}}"
     * )
     */
    private $middleName;

    /**
     * @ORM\Column(type="string", length=50,nullable=true)
     * @Groups("main")
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=50,nullable=true)
     * @Groups("main")
     * @Assert\Email()
     */
    private $secondEmail;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isRetired=false;

    /**
     * @return mixed
     */
    public function getisRetired()
    {
        return $this->isRetired;
    }

    /**
     * @param mixed $isRetired
     */
    public function setIsRetired($isRetired): void
    {
        $this->isRetired = $isRetired;
    }

    /**
     * @return mixed
     */
    public function getSecondEmail()
    {
        return $this->secondEmail;
    }

    /**
     * @param mixed $secondEmail
     */
    public function setSecondEmail($secondEmail): void
    {
        $this->secondEmail = $secondEmail;
    }


    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("main")
     * @Assert\Length(
     *     max=100,
     *     maxMessage="The maximum number of characters is {{limit}}"
     * )
     */
    private $section;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Assert\Length(
     *     max=100,
     *     maxMessage="The maximum number of characters is {{limit}}"
     * )
     */
    private $scrapperToken;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Assert\Length(
     *     max=100,
     *     maxMessage="The maximum number of characters is {{limit}}"
     * )
     */
    private $emailToken;

    /**
     * @return mixed
     */
    public function getEmailToken()
    {
        return $this->emailToken;
    }

    /**
     * @param mixed $emailToken
     */
    public function setEmailToken($emailToken): void
    {
        $this->emailToken = $emailToken;
    }

    /**
     * @return mixed
     */
    public function getScrapperToken()
    {
        return $this->scrapperToken;
    }

    /**
     * @param mixed $wosStatus
     */
    public function setScrapperToken($scrapperToken): void
    {
        $this->scrapperToken = $scrapperToken;
    }

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Assert\Length(
     *     max=100,
     *     maxMessage="The maximum number of characters is {{limit}}"
     * )
     */
    private $laboratory;


    /**
     * @ORM\OneToMany(targetEntity="UserArticle",mappedBy="user")
     */
    private $userArticles;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserProject", mappedBy="user", orphanRemoval=true)
     */
    private $userProjects;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Activity", mappedBy="user", orphanRemoval=true)
     */
    private $activities;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Citation", mappedBy="user", orphanRemoval=true)
     */
    private $citations;

    /**
     * @return mixed
     */
    public function getScrapToken()
    {
        return $this->scrapToken;
    }

    /**
     * @param mixed $scrapToken
     */
    public function setScrapToken($scrapToken): void
    {
        $this->scrapToken = $scrapToken;
    }

    /**
     * @ORM\Column(type="string",length=50,nullable=true)
     */
    private $scrapToken;

    /**
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\WosIdentificator",
     *      mappedBy="user",
     *     orphanRemoval=true,
     *     fetch="EXTRA_LAZY",
     *     cascade={"persist","remove"}
     * )
     * @Assert\Valid()
     */
    private $identificators;

    /**
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\UserScientificTitle",
     *      mappedBy="user",
     *     fetch="EXTRA_LAZY",
     *     orphanRemoval=true,
     *      cascade={"persist","remove"}
     *     )
     * @Assert\Valid()
     */
    private $scientificTitles;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\WorkInterruption", mappedBy="user", orphanRemoval=true)
     */
    private $workInterruptions;

    public function __construct()
    {
        $this->userArticles=new ArrayCollection();
        $this->userProjects = new ArrayCollection();
        $this->activities = new ArrayCollection();
        $this->citations = new ArrayCollection();
        $this->identificators = new ArrayCollection();
        $this->scientificTitles = new ArrayCollection();
        $this->workInterruptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getSection(): ?int
    {
        return $this->section;
    }

    public function setSection(?int $section): self
    {
        $this->section = $section;

        return $this;
    }

    public function getLaboratory(): ?string
    {
        return $this->laboratory;
    }

    public function setLaboratory(?string $laboratory): self
    {
        $this->laboratory = $laboratory;

        return $this;
    }





    /**
     * @return Collection|UserArticle[]
     */
    public function getUserArticles(): Collection
    {
        return $this->userArticles;
    }

    public function __toString()
    {
       return sprintf("%s %s %s",$this->firstName,$this->middleName,$this->lastName);
    }

    /**
     * @return mixed
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * @param mixed $middleName
     */
    public function setMiddleName($middleName): void
    {
        $this->middleName = $middleName;
    }

    /**
     * @return Collection|UserProject[]
     */
    public function getUserProjects(): Collection
    {
        return $this->userProjects;
    }

    public function addUserProject(UserProject $userProject): self
    {
        if (!$this->userProjects->contains($userProject)) {
            $this->userProjects[] = $userProject;
            $userProject->setUser($this);
        }

        return $this;
    }

    public function removeUserProject(UserProject $userProject): self
    {
        if ($this->userProjects->contains($userProject)) {
            $this->userProjects->removeElement($userProject);
            // set the owning side to null (unless already changed)
            if ($userProject->getUser() === $this) {
                $userProject->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Activity[]
     */
    public function getActivities(): Collection
    {
        return $this->activities;
    }

    public function addActivity(Activity $activity): self
    {
        if (!$this->activities->contains($activity)) {
            $this->activities[] = $activity;
            $activity->setUser($this);
        }

        return $this;
    }

    public function removeActivity(Activity $activity): self
    {
        if ($this->activities->contains($activity)) {
            $this->activities->removeElement($activity);
            // set the owning side to null (unless already changed)
            if ($activity->getUser() === $this) {
                $activity->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Citation[]
     */
    public function getCitations(): Collection
    {
        return $this->citations;
    }

    public function addCitation(Citation $citation): self
    {
        if (!$this->citations->contains($citation)) {
            $this->citations[] = $citation;
            $citation->setUser($this);
        }

        return $this;
    }

    public function removeCitation(Citation $citation): self
    {
        if ($this->citations->contains($citation)) {
            $this->citations->removeElement($citation);
            // set the owning side to null (unless already changed)
            if ($citation->getUser() === $this) {
                $citation->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|WosIdentificator[]
     */
    public function getIdentificators(): Collection
    {
        return $this->identificators;
    }

    public function addIdentificator(WosIdentificator $identificator): self
    {
        if (!$this->identificators->contains($identificator)) {
            $this->identificators[] = $identificator;
            $identificator->setUser($this);
        }

        return $this;
    }

    public function removeIdentificator(WosIdentificator $identificator): self
    {
        if ($this->identificators->contains($identificator)) {
            $this->identificators->removeElement($identificator);
            // set the owning side to null (unless already changed)
            if ($identificator->getUser() === $this) {
                $identificator->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserScientificTitle[]
     */
    public function getScientificTitles(): Collection
    {
        return $this->scientificTitles;
    }

    public function addScientificTitle(UserScientificTitle $scientificTitle): self
    {
        if (!$this->scientificTitles->contains($scientificTitle)) {
            $this->scientificTitles[] = $scientificTitle;
            $scientificTitle->setUser($this);
        }

        return $this;
    }

    public function removeScientificTitle(UserScientificTitle $scientificTitle): self
    {
        if ($this->scientificTitles->contains($scientificTitle)) {
            $this->scientificTitles->removeElement($scientificTitle);
            // set the owning side to null (unless already changed)
            if ($scientificTitle->getUser() === $this) {
                $scientificTitle->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|WorkInterruption[]
     */
    public function getWorkInterruptions(): Collection
    {
        return $this->workInterruptions;
    }

    public function addWorkInterruption(WorkInterruption $workInterruption): self
    {
        if (!$this->workInterruptions->contains($workInterruption)) {
            $this->workInterruptions[] = $workInterruption;
            $workInterruption->setUser($this);
        }

        return $this;
    }

    public function removeWorkInterruption(WorkInterruption $workInterruption): self
    {
        if ($this->workInterruptions->contains($workInterruption)) {
            $this->workInterruptions->removeElement($workInterruption);
            // set the owning side to null (unless already changed)
            if ($workInterruption->getUser() === $this) {
                $workInterruption->setUser(null);
            }
        }

        return $this;
    }


}
