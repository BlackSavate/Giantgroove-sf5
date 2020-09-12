<?php


namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TrackRepository")
 */
class Track
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isOpen;

    /**
     * @var string
     *
     * @ORM\Column(name="audio", type="string", length=255, nullable=true)
     * @Assert\File(
     *    mimeTypes={ "audio/*" },
     *    mimeTypesMessage="Veuillez choisir un fichier dans un format audio valide.",
     *    maxSize="30M")
     */
    private $audio;

    /**
     * @ORM\Column(type="float")
     */
    private $startTime;

    /**
     * @ORM\ManyToMany(targetEntity="Instrument", inversedBy="tracks")
     */
    private $instruments;

    /**
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="tracks")
     */
    private $project;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="tracks")
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity="Contribution", mappedBy="track")
     */
    private $contributions;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;


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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getIsOpen(): ?string
    {
        return $this->isOpen;
    }

    public function setIsOpen(bool $isOpen): self
    {
        $this->isOpen = $isOpen;

        return $this;
    }

    /**
     * Set audio
     *
     * @param string $audio
     *
     * @return Track
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

    /**
     * Set startTime
     *
     * @param float $startTime
     *
     * @return Track
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get startTime
     *
     * @return float
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Get the value of Project
     *
     * @return mixed
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set the value of Project
     *
     * @param mixed project
     *
     * @return Track
     */
    public function setProject($project)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get the value of Instruments
     *
     * @return mixed
     */
    public function getInstruments()
    {
        return $this->instruments;
    }

    /**
     * Set the value of Instruments
     *
     * @param mixed instruments
     *
     * @return Track
     */
    public function setInstruments($instruments)
    {
        $this->instruments = $instruments;

        return $this;
    }

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
     * @return Track
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get the value of Contributions
     *
     * @return mixed
     */
    public function getContributions()
    {
        return $this->contributions;
    }

    /**
     * Set the value of Contributions
     *
     * @param mixed contributions
     *
     * @return self
     */
    public function setContributions($contributions)
    {
        $this->contributions = $contributions;

        return $this;
    }

    /**
     * Set the value of Contributions
     *
     * @param Contribution contribution
     *
     * @return self
     */
    public function removeContribution(Contribution $contribution)
    {
        $this->contributions->removeElement($contribution);
        return $this;
    }

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