<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Contribution
 *
 * @ORM\Table(name="contribution")
 * @ORM\Entity(repositoryClass="App\Repository\ContributionRepository")
 */
class Contribution
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="audio", type="string", length=255)
     * @Assert\File(
     *    mimeTypes={ "audio/*" },
     *    mimeTypesMessage="Veuillez choisir un fichier dans un format audio valide. Le nom du fichier ne doit pas comporter de caractères spéciaux",
     *    maxSize="30M")
     */
    private $audio;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

//    /**
//     * @var string
//     *
//     * @ORM\Column(name="sheet", type="string", length=255, nullable=true)
//     */
//    private $sheet;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="contributions")
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="Track", inversedBy="contributions", cascade={"persist"})
     */
    private $track;

//    /**
//     * @ORM\OneToMany(targetEntity="Comment", mappedBy="contribution")
//     */
//    private $comments;

//    /**
//     * @ORM\OneToMany(targetEntity="UserVoteContribution", mappedBy="contribution")
//     */
//    private $votes;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Contribution
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Set audio
     *
     * @param string $audio
     *
     * @return Contribution
     */
    public function setAudio($audio)
    {
        $this->audio = $audio;

        return $this;
    }

    /**
     * Get audio
     *
     * @return string
     */
    public function getAudio()
    {
        return $this->audio;
    }

//    /**
//     * Set sheet
//     *
//     * @param string $sheet
//     *
//     * @return Contribution
//     */
//    public function setSheet($sheet)
//    {
//        $this->sheet = $sheet;
//
//        return $this;
//    }
//
//    /**
//     * Get sheet
//     *
//     * @return string
//     */
//    public function getSheet()
//    {
//        return $this->sheet;
//    }

    /**
     * Get the value of Author
     *
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set the value of Author
     *
     * @param mixed author
     *
     * @return self
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get the value of Track
     *
     * @return mixed
     */
    public function getTrack()
    {
        return $this->track;
    }

    /**
     * Set the value of Track
     *
     * @param mixed track
     *
     * @return self
     */
    public function setTrack($track)
    {
        $this->track = $track;

        return $this;
    }

//    /**
//     * Get the value of Comments
//     *
//     * @return mixed
//     */
//    public function getComments()
//    {
//        return $this->comments;
//    }
//
//    /**
//     * Set the value of Comments
//     *
//     * @param mixed comments
//     *
//     * @return self
//     */
//    public function setComments($comments)
//    {
//        $this->comments = $comments;
//
//        return $this;
//    }
//
//    /**
//     * Get the value of votes
//     *
//     * @return mixed
//     */
//    public function getvotes()
//    {
//        return $this->votes;
//    }
//
//    /**
//     * Set the value of votes
//     *
//     * @param mixed votes
//     *
//     * @return self
//     */
//    public function setvotes($votes)
//    {
//        $this->votes = $votes;
//
//        return $this;
//    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

}
