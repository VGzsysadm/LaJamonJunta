<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 */
class Post
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="posts")
     */
    private $user;

    /**
     * @ORM\Column(type="text")
     */
    private $posted;

    /**
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $modified;

    /**
     * @ORM\Column(type="text")
     */
    private $tittle;

    /**
     * @ORM\Column(type="boolean")
     */
    private $pinned;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Pcomments", mappedBy="post", orphanRemoval=true)
     */
    private $pcomments;

    public function __construct()
    {
        $this->pcomments = new ArrayCollection();
    }

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

    public function getPosted(): ?string
    {
        return $this->posted;
    }

    public function setPosted(string $posted): self
    {
        $this->posted = $posted;

        return $this;
    }

    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTimeInterface $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getModified(): ?\DateTimeInterface
    {
        return $this->modified;
    }

    public function setModified(?\DateTimeInterface $modified): self
    {
        $this->modified = $modified;

        return $this;
    }

    public function getTittle(): ?string
    {
        return $this->tittle;
    }

    public function setTittle(string $tittle): self
    {
        $this->tittle = $tittle;

        return $this;
    }

    public function getPinned(): ?bool
    {
        return $this->pinned;
    }

    public function setPinned(bool $pinned): self
    {
        $this->pinned = $pinned;

        return $this;
    }

    /**
     * @return Collection|Pcomments[]
     */
    public function getPcomments(): Collection
    {
        return $this->pcomments;
    }

    public function addPcomment(Pcomments $pcomment): self
    {
        if (!$this->pcomments->contains($pcomment)) {
            $this->pcomments[] = $pcomment;
            $pcomment->setPost($this);
        }

        return $this;
    }

    public function removePcomment(Pcomments $pcomment): self
    {
        if ($this->pcomments->contains($pcomment)) {
            $this->pcomments->removeElement($pcomment);
            // set the owning side to null (unless already changed)
            if ($pcomment->getPost() === $this) {
                $pcomment->setPost(null);
            }
        }

        return $this;
    }
}
