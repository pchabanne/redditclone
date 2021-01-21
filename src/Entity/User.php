<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity("email")
 * @UniqueEntity("username")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=12, unique=true)
     * @Assert\Regex("^[A-Za-z0-9_-]*$")
     * @Assert\Unique
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Email
     * @Assert\Unique
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="user", orphanRemoval=true)
     */
    private $post;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="user", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $roles = [];

    /**
     * user constructor
     */
    public function __construct()
    {
        $this->post = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->roles =['ROLE_USER'];
    }

    /**
     * get the id of the user
     *
     * @return integer|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * get the username of the user
     *
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * set the username of the user
     *
     * @param string $username
     * @return self
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * get the password of the user
     *
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * set the password of the user
     *
     * @param string $password
     * @return self
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * get the user's email
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * set the user's email
     *
     * @param string $email
     * @return self
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * get all posts posted by this user
     *
     * @return Collection
     */
    public function getPost(): Collection
    {
        return $this->post;
    }

    /**
     * add a post posted by this user
     *
     * @param Post $post
     * @return self
     */
    public function addPost(Post $post): self
    {
        if (!$this->post->contains($post)) {
            $this->post[] = $post;
            $post->setUser($this);
        }

        return $this;
    }

    /**
     * remove a post posted by this user
     *
     * @param Post $post
     * @return self
     */
    public function removePost(Post $post): self
    {
        if ($this->post->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getUser() === $this) {
                $post->setUser(null);
            }
        }

        return $this;
    }

   
    /**
     * get all comments posted by this user
     *
     * @return Collection
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setUser($this);
        }

        return $this;
    }

    /**
     * remove a comment posted by this user
     *
     * @param Comment $comment
     * @return self
     */
    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getSalt(){
        return null;
    }

    public function eraseCredentials()
    {
    }

    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
            $this->password,
        ]);
    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->username,
            $this->password,
        ) = unserialize($serialized, ['allow_classes'=> false]);
    }

    public function setRoles(?array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
}
