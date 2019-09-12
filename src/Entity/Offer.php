<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OfferRepository")
 */
class Offer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="offers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Provider", inversedBy="offers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $provider;

    /**
     * @ORM\Column(type="text", nullable=false))
     * @Assert\Length(
     *      min = 20,
     *      max = 500,
     *      minMessage = "La descripción tiene que tener un mínimo de {{ limit }} carácteres.",
     *      maxMessage = "La oferta tiene que tener un máximo de {{ limit }} carácteres.",
     * )
     */
    private $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $offerpicture;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getProvider(): ?Provider
    {
        return $this->provider;
    }

    public function setProvider(?Provider $provider): self
    {
        $this->provider = $provider;

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

    public function getOfferpicture(): ?string
    {
        return $this->offerpicture;
    }

    public function setOfferpicture(?string $offerpicture): self
    {
        $this->offerpicture = $offerpicture;

        return $this;
    }
}
