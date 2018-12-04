<?php

namespace App\Entity\Security;

use App\Entity\CreditCardConsume;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Security\UserRepository")
 * @UniqueEntity(fields="email", message="Email already taken")
 * @method getPlainPassword()
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email(
     *     message = "the email '{{ value }}' is nor a valid email."
     * )
     */
    private $email;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     * @Assert\Length(max=4096)
     */
    private $password;

    /**
     * @var array
     * @ORM\Column(type="simple_array")
     */
    private $roles = [];

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CreditCardConsume", mappedBy="user")
     */
    private $creditCardConsumes;

    public function __construct()
    {
        $this->roles = array('ROLE_USER');
        $this->creditCardConsumes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
       return (string) $this->email;
    }

    /**
     * @return Collection|CreditCardConsume[]
     */
    public function getCreditCardConsumes(): Collection
    {
        return $this->creditCardConsumes;
    }

    public function addCreditCardConsume(CreditCardConsume $creditCardConsume): self
    {
        if (!$this->creditCardConsumes->contains($creditCardConsume)) {
            $this->creditCardConsumes[] = $creditCardConsume;
            $creditCardConsume->setUser($this);
        }

        return $this;
    }

    public function removeCreditCardConsume(CreditCardConsume $creditCardConsume): self
    {
        if ($this->creditCardConsumes->contains($creditCardConsume)) {
            $this->creditCardConsumes->removeElement($creditCardConsume);
            // set the owning side to null (unless already changed)
            if ($creditCardConsume->getUser() === $this) {
                $creditCardConsume->setUser(null);
            }
        }

        return $this;
    }
}