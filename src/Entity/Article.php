<?php

namespace App\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 */
class Article
{
    use TimestampableEntity;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=300)
     * @Assert\NotBlank()
     * @Assert\Length(
     *     max=300,
     *     maxMessage="The maximum number of characters is {{limit}}"
     * )
     */
    private $title;

    /**
     * @ORM\Column(type="string",length=100)
     * @Assert\NotBlank()
     */
    private $type;

    /**
     * @ORM\Column(type="string",length=100,nullable=true)
     *
     */
    private $pType;

    /**
     * @ORM\Column(type="string",length=200,nullable=true)
     *
     */
    private $miscellanous;

    /**
     * @ORM\Column(type="string",length=100,nullable=true)
     * @Assert\Length(
     *     max=20,
     *     maxMessage="The maximum number of characters is {{limit}}"
     * )
     *
     */
    private $pages;

    /**
     *
     * @ORM\Column(type="integer",nullable=true)
     * @Assert\Type(
     *     type="integer",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $totalPages=0;


    /**
     * @ORM\Column(type="string", length=555)
     * @Assert\NotBlank()
     * @Assert\Length(
     *     max=555,
     *     maxMessage="The maximum number of characters is {{limit}}"
     * )
     */
    private $authors;

    /**
     * @ORM\Column(type="text", length=5500, nullable=true)
     *
     * @Assert\Length(
     *     max=5500,
     *     maxMessage="The maximum number of characters is {{ limit }}"
     * )
     */
    private $abstract;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     * @Assert\Length(
     *     max=1000,
     *     maxMessage="The maximum number of characters is {{ limit }}"
     * )
     */
    private $doi;

    /**
     * @ORM\Column(type="string", length=1500)
     * @Assert\NotBlank()
     * @Assert\Length(
     *     max=1500,
     *     maxMessage="The maximum number of characters is {{ limit }}"
     * )
     */
    private $journal;

    /**
     * @ORM\Column(type="string", length=100,nullable=true)
     * @Assert\Length(
     *     max=100,
     *     maxMessage="The maximum number of characters is {{ limit }}"
     * )
     */
    private $volume;

    /**
     * @return mixed
     */
    public function getVolume()
    {
        return $this->volume;
    }

    /**
     * @param mixed $volume
     */
    public function setVolume($volume): void
    {
        $this->volume = $volume;
    }

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     *
     *
     */
    private $effectiveAuthorsNumber;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     * @Assert\Type(
     *     type="double",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     *
     */
    private $AIS;

    /**
     * @ORM\Column(type="string", length=2555, nullable=true)
     * @Assert\Length(
     *     max=1555,
     *     maxMessage="The maximum number of characters is {{limit}}"
     * )
     */
    private $corespondingAuthors;

    /**
     * @ORM\Column(type="string", length=2555, nullable=true)
     * @Assert\Length(
     *     max=1555,
     *     maxMessage="The maximum number of characters is {{limit}}"
     * )
     */
    private $emailsOfCorespondingAuthors;

    /**
     * @ORM\Column(type="string", length=555, nullable=true)
     * @Assert\Length(
     *     max=555,
     *     maxMessage="The maximum number of characters is {{limit}}"
     * )
     */
    private $primeAuthors;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $publicationDate;

    /**
     * @ORM\OneToMany(
     *     targetEntity="UserArticle",
     *     mappedBy="article",
     *     fetch="EXTRA_LAZY",
     *     orphanRemoval=true,
     *     cascade={"persist"}
     *
     * )
     * @Assert\Valid()
     */
    private $articleAuthors;

    /**
     * @ORM\Column(type="integer",nullable=true,options={"default" : 1})
     * @Assert\Type(
     *     type="integer",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $theNumberOfPrimeAuthors=1;

    /**
     * @ORM\Column(type="integer",nullable=true)
     * @Assert\Type(
     *     type="integer",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $theNumberOfCorrespondingAuthors;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\Type(
     *     type="bool",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $thetaFunction=false;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    private $scrapperToken;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\Type(
     *     type="bool",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $isUpdatedByAdmin=false;

    /**
     * @return mixed
     */
    public function getIsUpdatedByAdmin()
    {
        return $this->isUpdatedByAdmin;
    }

    /**
     * @param mixed $updatedByAdmin
     */
    public function setIsUpdatedByAdmin($isUpdatedByAdmin): void
    {
        $this->isUpdatedByAdmin = $isUpdatedByAdmin;
    }

    /**
     * @ORM\OnetoMany(targetEntity="ArticleCitation",mappedBy="article",cascade={"persist","remove"})
     */
    private $citations;

    /**
     * @return mixed
     */
    public function getCitations()
    {
        return $this->citations;
    }

    /**
     * @param mixed $citations
     * @return null|Article
     */
    public function addCitation(ArticleCitation $citation): ?self
    {

        if($this->citations->contains($citation)){
            return null;
        }
        $this->citations[]=$citation;
        $citation->setArticle($this);

        return $this;
    }

    public function removeCitation(ArticleCitation $citation):?self
    {
        if(!$this->citations->contains($citation)){
            return null;
        }
        $this->citations->removeElement($citation);
        $citation->setArticle(null);
        return $this;
    }

    public function __construct()
    {
        $this->articleAuthors=new ArrayCollection();
        $this->citations=new Arraycollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getAuthors(): ?string
    {
        return $this->authors;
    }

    public function setAuthors(string $authors): self
    {
        $this->authors = $authors;

        return $this;
    }

    public function getAbstract(): ?string
    {
        return $this->abstract;
    }

    public function setAbstract(?string $abstract): self
    {
        $this->abstract = $abstract;

        return $this;
    }

    public function getDoi(): ?string
    {
        return $this->doi;
    }

    public function setDoi(?string $doi): self
    {
        $this->doi = $doi;

        return $this;
    }

    public function getJournal(): ?string
    {
        return $this->journal;
    }

    public function setJournal(string $journal): self
    {
        $this->journal = $journal;

        return $this;
    }

    public function getEffectiveAuthorsNumber(): ?float
    {
        return $this->effectiveAuthorsNumber;
    }

    public function setEffectiveAuthorsNumber(?float $effectiveAuthorsNumber): self
    {
        $this->effectiveAuthorsNumber = $effectiveAuthorsNumber;

        return $this;
    }

    public function getAIS()
    {
        return $this->AIS;
    }

    public function setAIS($AIS): self
    {
        $this->AIS = $AIS;

        return $this;
    }

    public function getCorespondingAuthors(): ?string
    {
        return $this->corespondingAuthors;
    }

    public function setCorespondingAuthors(?string $corespondingAuthors): self
    {
        $this->corespondingAuthors = $corespondingAuthors;

        return $this;
    }

    public function getEmailsOfCorespondingAuthors(): ?string
    {
        return $this->emailsOfCorespondingAuthors;
    }

    public function setEmailsOfCorespondingAuthors(?string $emailsOfCorespondingAuthors): self
    {
        $this->emailsOfCorespondingAuthors = $emailsOfCorespondingAuthors;

        return $this;
    }

    public function getPrimeAuthors(): ?string
    {
        return $this->primeAuthors;
    }

    public function setPrimeAuthors(?string $primeAuthors): self
    {
        $this->primeAuthors = $primeAuthors;

        return $this;
    }

    public function getPublicationDate(): ?\DateTimeInterface
    {
        return $this->publicationDate;
    }

    public function setPublicationDate(?\DateTimeInterface $publicationDate): self
    {
        $this->publicationDate = $publicationDate;

        return $this;
    }

    public function getArticleAuthors():?Collection
    {
        return $this->articleAuthors;
    }

    public function getTheNumberOfPrimeAuthors(): ?int
    {
        return $this->theNumberOfPrimeAuthors;
    }

    public function setTheNumberOfPrimeAuthors(int $theNumberOfPrimeAuthors): self
    {
        $this->theNumberOfPrimeAuthors = $theNumberOfPrimeAuthors;

        return $this;
    }

    public function getTheNumberOfCorrespondingAuthors(): ?int
    {
        return $this->theNumberOfCorrespondingAuthors;
    }

    public function setTheNumberOfCorrespondingAuthors(int $theNumberOfCorrespondingAuthors): self
    {
        $this->theNumberOfCorrespondingAuthors = $theNumberOfCorrespondingAuthors;

        return $this;
    }

    public function getThetaFunction(): ?bool
    {
        return $this->thetaFunction;
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
    public function setType($type): void
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getPType()
    {
        return $this->pType;
    }

    /**
     * @param mixed $pType
     */
    public function setPType($pType): void
    {
        $this->pType = $pType;
    }

    /**
     * @return mixed
     */
    public function getMiscellanous()
    {
        return $this->miscellanous;
    }

    /**
     * @param mixed $miscellanous
     */
    public function setMiscellanous($miscellanous): void
    {
        $this->miscellanous = $miscellanous;
    }

    /**
     * @return mixed
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * @param mixed $pages
     */
    public function setPages($pages): void
    {
        $this->pages = $pages;
    }

    /**
     * @return mixed
     */
    public function getTotalPages()
    {
        return $this->totalPages;
    }

    /**
     * @param mixed $totalPages
     */
    public function setTotalPages($totalPages): void
    {
        $this->totalPages = $totalPages;
    }



    public function setThetaFunction(bool $thetaFunction): self
    {
        $this->thetaFunction = $thetaFunction;

        return $this;
    }

    public function addArticleAuthor(UserArticle $userArticle):?self
    {
        if($this->articleAuthors->contains($userArticle)){
            return null;
        }
        $this->articleAuthors[]=$userArticle;
        $userArticle->setArticle($this);

        return $this;

    }

    public function removeArticleAuthor(UserArticle $userArticle):?self
    {
        if(!$this->articleAuthors->contains($userArticle)){
            return null;
        }
        $this->articleAuthors->removeElement($userArticle);
        $userArticle->setArticle(null);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getScrapperToken()
    {
        return $this->scrapperToken;
    }

    /**
     * @param mixed $scrapperToken
     */
    public function setScrapperToken($scrapperToken): void
    {
        $this->scrapperToken = $scrapperToken;
    }


}
