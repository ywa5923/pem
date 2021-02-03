<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class UserArticle
 * @package App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\UserArticleRepository")
 * @UniqueEntity(
 *     fields={"user","article"},
 *     errorPath="user",
 *     message="This user was already attached to this article"
 *
 * )
 */
class UserArticle
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Article", inversedBy="articleAuthors")
     * @Assert\NotNull(message="Please set an article")
     *
     */
    private $article;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="userArticles")
     * @Assert\NotNull(message="Please set an author")
     *
     */
    private $user;


    /**
     * @ORM\Column(type="boolean")
     */
    private $isPrimeAuthor=false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isCorrespondingAuthor=false;





    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

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
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }



    /**
     * @return mixed
     */
    public function getIsPrimeAuthor()
    {
        return $this->isPrimeAuthor;
    }

    /**
     * @param mixed $isPrimeAuthor
     */
    public function setIsPrimeAuthor($isPrimeAuthor): void
    {
        $this->isPrimeAuthor = $isPrimeAuthor;
    }

    /**
     * @return mixed
     */
    public function getIsCorrespondingAuthor()
    {
        return $this->isCorrespondingAuthor;
    }

    /**
     * @param mixed $isCorrespondingAuthor
     */
    public function setIsCorrespondingAuthor($isCorrespondingAuthor): void
    {
        $this->isCorrespondingAuthor = $isCorrespondingAuthor;
    }


}