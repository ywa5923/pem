<?php

namespace App\Entity;


use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class ArticleCitation
 * @package App\Entity
 * @ORM\Entity()
 */
class ArticleCitation
{

    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Article", inversedBy="citations")
     */
    protected $article;

    /**
     * @ORM\Column(type="text", length=5000)
     */
    protected $title;

    /**
     * @ORM\Column(type="text", length=5000)
     */
    protected $authors;

    /**
     * @ORM\Column(type="text",length=5000)
     */
    protected $journal;

    /**
     * @ORM\Column(type="text",length=1000,nullable=true)
     */
    protected $doi;

    /**
     * @ORM\Column(type="date",nullable=true)
     */

    protected $publicationDate;

    /**
     * @return mixed
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @param mixed $article
     */
    public function setArticle($article): void
    {
        $this->article = $article;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getAuthors()
    {
        return $this->authors;
    }

    /**
     * @param mixed $authors
     */
    public function setAuthors($authors): void
    {
        $this->authors = $authors;
    }

    /**
     * @return mixed
     */
    public function getJournal()
    {
        return $this->journal;
    }

    /**
     * @param mixed $journal
     */
    public function setJournal($journal): void
    {
        $this->journal = $journal;
    }

    /**
     * @return mixed
     */
    public function getDoi()
    {
        return $this->doi;
    }

    /**
     * @param mixed $doi
     */
    public function setDoi($doi): void
    {
        $this->doi = $doi;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return mixed
     */
    public function getPublicationDate():?\DateTimeInterface
    {
        return $this->publicationDate;
    }

    public function setPublicationDate(\DateTimeInterface $publicationDate)
    {
        $this->publicationDate=$publicationDate;
    }


}
