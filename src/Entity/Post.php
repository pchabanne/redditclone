<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 */
class Post
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank()
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="post")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="post", orphanRemoval=true, cascade={"persist"})
     */
    private $comments;

    /**
     * @ORM\ManyToOne(targetEntity=Subreddit::class, inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $subreddit;

    /**
     * @ORM\OneToMany(targetEntity=PostLike::class, mappedBy="post", cascade={"persist"})
     */
    private $likes;

    /**
     * post constructor
     */
    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->created_at = new \DateTime();
        $this->likes = new ArrayCollection();
    }

    /**
     * return id of the post
     *
     * @return integer|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * get the title of the post
     *
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * set the title of the post
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
     * return slug of the title
     *
     * @return string
     */
    public function getSlug(): string
    {
        return (new Slugify())->slugify($this->title);
    }

    /**
     * get the content of the post
     *
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * set the content of the post
     *
     * @param string|null $content
     * @return self
     */
    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * get the date when the post is posted
     *
     * @return \DateTimeInterface|null
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    /**
     * set the date when the post is posted
     *
     * @param \DateTimeInterface $created_at
     * @return self
     */
    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * get the user who posts the post
     *
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * set the user who posts the post
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
     * get the comments of the post
     *
     * @return Collection
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * add a comment to the post
     *
     * @param Comment $comment
     * @return self
     */
    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setPost($this);
        }

        return $this;
    }

    /**
     * remove a comment
     *
     * @param Comment $comment
     * @return self
     */
    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }

    /**
     * get the subreddit in which the post is belonging
     *
     * @return Subreddit|null
     */
    public function getSubreddit(): ?Subreddit
    {
        return $this->subreddit;
    }

    /**
     * set the subreddit in which the post is belonging
     *
     * @param Subreddit|null $subreddit
     * @return self
     */
    public function setSubreddit(?Subreddit $subreddit): self
    {
        $this->subreddit = $subreddit;

        return $this;
    }

    /**
     * @return Collection|PostLike[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function getCountLikes(){
        $count = 0;
        $likes=$this->getLikes();
        foreach($likes as $like){
            if($like->getValue()==true){
                $count = $count+1;
            }
            elseif($like->getValue()==false){
                $count = $count-1;
            }
        }

        return $count;
    }

    public function addLike(PostLike $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setPost($this);
        }

        return $this;
    }

    public function removeLike(PostLike $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getPost() === $this) {
                $like->setPost(null);
            }
        }

        return $this;
    }

    public function isLikedByUser($user){
        foreach($this->likes as $like){
            if($like->getUser()==$user && $like->getValue()==true){
                return true;
            }
        }
        return false;
    }

    public function isDislikedByUser($user){
        foreach($this->likes as $like){
            if($like->getUser()==$user && $like->getValue()==false){
                return true;
            }
        }
        return false;
    }
}
