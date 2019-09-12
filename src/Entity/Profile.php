<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="lc_profile")
 * @ORM\Entity(repositoryClass="App\Repository\ProfileRepository")
 */
class Profile
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="profile", cascade={"persist", "remove"})
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\File(mimeTypes={ "image/gif","image/jpg","image/jpeg" }, maxSize="1000k")
     * @Assert\Image(
     *     minWidth = 300,
     *     maxWidth = 300,
     *     minHeight = 300,
     *     maxHeight = 300
     * )
     */
    private $avatar;

    /**
     * @ORM\Column(type="datetime")
     */
    private $registration;

    /**
     * @ORM\Column(type="boolean")
     */
    private $terms;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastlogin;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ip;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?User
    {
        return $this->username;
    }

    public function setUsername(?User $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getRegistration(): ?\DateTimeInterface
    {
        return $this->registration;
    }

    public function setRegistration(\DateTimeInterface $registration): self
    {
        $this->registration = $registration;

        return $this;
    }

    public function getTerms(): ?bool
    {
        return $this->terms;
    }

    public function setTerms(bool $terms): self
    {
        $this->terms = $terms;

        return $this;
    }

    public function getLastLogin(): ?\DateTimeInterface
    {
        return $this->lastlogin;
    }

    public function setLastLogin(\DateTimeInterface $lastlogin): self
    {
        $this->lastlogin = $lastlogin;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function __construct()
    {
        $this->terms = true;
        $this->registration = new \DateTime("now");
        $this->avatar = "default_avatar.jpg";
        $this->comments = new ArrayCollection();
    }
}
