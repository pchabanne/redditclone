<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 */
class Comment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Post::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $post;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * comment constructor
     */
    public function __construct()
    {
        $this->created_at = new \DateTime();
    }

    /**
     * return id of the comment
     *
     * @return integer|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * return content of the comment
     *
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * set content of the comment
     *
     * @param string $content
     * @return self
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * get the user who posts the comment
     *
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * set the user who posts the comment
     *
     * @param User|null $user
     * @return self
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * get the post on which the comment is posted
     *
     * @return Post|null
     */
    public function getPost(): ?Post
    {
        return $this->post;
    }

    /**
     * set the post on which the comment is posted
     *
     * @param Post|null $post
     * @return self
     */
    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    /**
     * get the date when the comment is posted
     *
     * @return \DateTimeInterface|null
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    /**
     * set the date when the comment is posted
     *
     * @param \DateTimeInterface $created_at
     * @return self
     */
    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }
}
