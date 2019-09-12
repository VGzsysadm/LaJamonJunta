<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProviderRepository")
 */
class Provider
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
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="providers")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Activity", mappedBy="provider")
     */
    private $activities;

    /**
     * @ORM\Column(type="smallint")
     */
    private $active;

    /**
     * @ORM\Column(type="text", nullable=false))
     * @Assert\Length(
     *      min = 20,
     *      minMessage = "La descripción tiene que tener un mínimo de {{ limit }} carácteres.",
     * )
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $location;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $state;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $web;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\File(mimeTypes={ "application/pdf" }, maxSize="3500k")
     */
    private $documentone;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\File(mimeTypes={ "image/jpg","image/jpeg" }, maxSize="350k")
     * @Assert\Image(
     *     minWidth = 1024,
     *     minHeight = 600
     * )
     */
    private $picture;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Offer", mappedBy="provider", orphanRemoval=true)
     */
    private $offers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Ocomment", mappedBy="provider", orphanRemoval=true)
     */
    private $ocomments;

    public function __construct()
    {
        $this->activities = new ArrayCollection();
        $this->offers = new ArrayCollection();
        $this->ocomments = new ArrayCollection();
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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
            $activity->setProvider($this);
        }

        return $this;
    }

    public function removeActivity(Activity $activity): self
    {
        if ($this->activities->contains($activity)) {
            $this->activities->removeElement($activity);
            // set the owning side to null (unless already changed)
            if ($activity->getProvider() === $this) {
                $activity->setProvider(null);
            }
        }

        return $this;
    }

    public function getActive(): ?int
    {
        return $this->active;
    }

    public function setActive(int $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getWeb(): ?string
    {
        return $this->web;
    }

    public function setWeb(?string $web): self
    {
        $this->web = $web;

        return $this;
    }

    public function getDocumentone(): ?string
    {
        return $this->documentone;
    }

    public function setDocumentone(?string $documentone): self
    {
        $this->documentone = $documentone;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * @return Collection|Offer[]
     */
    public function getOffers(): Collection
    {
        return $this->offers;
    }

    public function addOffer(Offer $offer): self
    {
        if (!$this->offers->contains($offer)) {
            $this->offers[] = $offer;
            $offer->setProvider($this);
        }

        return $this;
    }

    public function removeOffer(Offer $offer): self
    {
        if ($this->offers->contains($offer)) {
            $this->offers->removeElement($offer);
            // set the owning side to null (unless already changed)
            if ($offer->getProvider() === $this) {
                $offer->setProvider(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Ocomment[]
     */
    public function getOcomments(): Collection
    {
        return $this->ocomments;
    }

    public function addOcomment(Ocomment $ocomment): self
    {
        if (!$this->ocomments->contains($ocomment)) {
            $this->ocomments[] = $ocomment;
            $ocomment->setProvider($this);
        }

        return $this;
    }

    public function removeOcomment(Ocomment $ocomment): self
    {
        if ($this->ocomments->contains($ocomment)) {
            $this->ocomments->removeElement($ocomment);
            // set the owning side to null (unless already changed)
            if ($ocomment->getProvider() === $this) {
                $ocomment->setProvider(null);
            }
        }

        return $this;
    }
}
