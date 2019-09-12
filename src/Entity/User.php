<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use App\Validator\Constraints as CustomAssert;

/**
 * @ORM\Table(name="lc_user")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields="email", message="Email already taken")
 * @UniqueEntity(fields="username", message="Username already taken")
 */
class User implements AdvancedUserInterface, \Serializable
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
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(
     *     message = "El email '{{ value }}' no es un email válido.",
     *     checkMX = true,
     *     checkHost = true
     * )
     * @CustomAssert\Email(domains = {"yahoo.com", "gmail.com", "outlook.es", "outlook.com", "hotmail.com", "protonmail.com", "yandex.com", "protonmail.ch", "tutanota.com", "tutanota.de", "tutamail.com", "tuta.io", "keemail.me", "icloud.com"})
     */
    private $email;

    /**
     * @Assert\Length(max=4096)
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="array")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Profile", mappedBy="username", cascade={"persist", "remove"})
     */
    private $profile;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Token", mappedBy="username", cascade={"persist", "remove"})
     */
    private $token;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comments", mappedBy="user")
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comments", mappedBy="target")
     */
    private $targetcomment;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Hierarchy", mappedBy="user", cascade={"persist", "remove"})
     */
    private $hierarchy;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Award", mappedBy="user", cascade={"persist", "remove"})
     */
    private $award;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Provider", mappedBy="user")
     */
    private $providers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Activity", mappedBy="username")
     */
    private $activities;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Activity", mappedBy="target")
     */
    private $targetactivities;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="user")
     */
    private $posts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Offer", mappedBy="user", orphanRemoval=true)
     */
    private $offers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Pcomments", mappedBy="user", orphanRemoval=true)
     */
    private $pcomments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Ocomment", mappedBy="user", orphanRemoval=true)
     */
    private $ocomments;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }
    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getisActive(): ?bool
    {
        return $this->isActive;
    }

    public function setisActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(?Profile $profile): self
    {
        $this->profile = $profile;

        // set (or unset) the owning side of the relation if necessary
        $newUsername = $profile === null ? null : $this;
        if ($newUsername !== $profile->getUsername()) {
            $profile->setUsername($newUsername);
        }

        return $this;
    }

    public function getToken(): ?Token
    {
        return $this->token;
    }

    public function setToken(?Token $token): self
    {
        $this->token = $token;

        // set (or unset) the owning side of the relation if necessary
        $newUsername = $token === null ? null : $this;
        if ($newUsername !== $token->getUsername()) {
            $token->setUsername($newUsername);
        }

        return $this;
    }
    public function __construct()
    {
        $this->roles = array('ROLE_USER');
        $this->isActive = false;
        $this->comments = new ArrayCollection();
        $this->providers = new ArrayCollection();
        $this->activities = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->offers = new ArrayCollection();
        $this->pcomments = new ArrayCollection();
        $this->ocomments = new ArrayCollection();
    }
       /** @see \Serializable::serialize() */
       public function serialize()
       {
           return serialize(array(
               $this->id,
               $this->username,
               $this->password,
               $this->isActive,
               // see section on salt below
               // $this->salt,
           ));
       }
       /** @see \Serializable::unserialize() */
       public function unserialize($serialized)
       {
           list (
               $this->id,
               $this->username,
               $this->password,
               $this->isActive,
               // see section on salt below
               // $this->salt
               ) = unserialize($serialized, ['allowed_classes' => false]);
       }
    public function eraseCredentials()
    {

    }
    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }
    public function isAccountNonExpired()
    {
        return true;
    }
    public function isAccountNonLocked()
    {
        return true;
    }
    public function isCredentialsNonExpired()
    {
        return true;
    }
    public function isEnabled()
    {
        return $this->isActive;
    }

    /**
     * @return Collection|Comments[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    public function getHierarchy(): ?Hierarchy
    {
        return $this->hierarchy;
    }

    public function setHierarchy(?Hierarchy $hierarchy): self
    {
        $this->hierarchy = $hierarchy;

        // set (or unset) the owning side of the relation if necessary
        $newUser = $hierarchy === null ? null : $this;
        if ($newUser !== $hierarchy->getUser()) {
            $hierarchy->setUser($newUser);
        }

        return $this;
    }

    public function getAward(): ?Award
    {
        return $this->award;
    }

    public function setAward(?Award $award): self
    {
        $this->award = $award;

        // set (or unset) the owning side of the relation if necessary
        $newUser = $award === null ? null : $this;
        if ($newUser !== $award->getUser()) {
            $award->setUser($newUser);
        }

        return $this;
    }

    /**
     * @return Collection|Provider[]
     */
    public function getProviders(): Collection
    {
        return $this->providers;
    }

    public function addProvider(Provider $provider): self
    {
        if (!$this->providers->contains($provider)) {
            $this->providers[] = $provider;
            $provider->setUser($this);
        }

        return $this;
    }

    public function removeProvider(Provider $provider): self
    {
        if ($this->providers->contains($provider)) {
            $this->providers->removeElement($provider);
            // set the owning side to null (unless already changed)
            if ($provider->getUser() === $this) {
                $provider->setUser(null);
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
            $activity->setUsername($this);
        }

        return $this;
    }

    public function removeActivity(Activity $activity): self
    {
        if ($this->activities->contains($activity)) {
            $this->activities->removeElement($activity);
            // set the owning side to null (unless already changed)
            if ($activity->getUsername() === $this) {
                $activity->setUsername(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setUser($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            // set the owning side to null (unless already changed)
            if ($post->getUser() === $this) {
                $post->setUser(null);
            }
        }

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
            $offer->setUser($this);
        }

        return $this;
    }

    public function removeOffer(Offer $offer): self
    {
        if ($this->offers->contains($offer)) {
            $this->offers->removeElement($offer);
            // set the owning side to null (unless already changed)
            if ($offer->getUser() === $this) {
                $offer->setUser(null);
            }
        }

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
            $pcomment->setUser($this);
        }

        return $this;
    }

    public function removePcomment(Pcomments $pcomment): self
    {
        if ($this->pcomments->contains($pcomment)) {
            $this->pcomments->removeElement($pcomment);
            // set the owning side to null (unless already changed)
            if ($pcomment->getUser() === $this) {
                $pcomment->setUser(null);
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
            $ocomment->setUser($this);
        }

        return $this;
    }

    public function removeOcomment(Ocomment $ocomment): self
    {
        if ($this->ocomments->contains($ocomment)) {
            $this->ocomments->removeElement($ocomment);
            // set the owning side to null (unless already changed)
            if ($ocomment->getUser() === $this) {
                $ocomment->setUser(null);
            }
        }

        return $this;
    }
}
