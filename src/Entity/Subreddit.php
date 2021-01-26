<?php

namespace App\Entity;

use App\Repository\SubredditRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SubredditRepository::class)
 * @UniqueEntity("title")
 */
class Subreddit
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=15, unique=true)
     * @Assert\Regex(
     *      pattern= "^[A-Za-z0-9]*$",
     *      match=false
     * )
     */
    private $title;

    /**
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="subreddit", orphanRemoval=true)
     */
    private $posts;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="subreddits")
     */
    private $users;

    /**
     * subreddit constructor
     */
    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    /**
     * get the id of the subreddit
     *
     * @return integer|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * get the title of the subreddit
     *
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * set the title of the subreddit
     *
     * @param string $title
     * @return self
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * get all posts of the subreddit
     *
     * @return Collection
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    /**
     * add a post to the subreddit
     *
     * @param Post $post
     * @return self
     */
    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setSubreddit($this);
        }

        return $this;
    }

    /**
     * remove a post from the subreddit
     *
     * @param Post $post
     * @return self
     */
    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getSubreddit() === $this) {
                $post->setSubreddit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->users->removeElement($user);

        return $this;
    }
}
