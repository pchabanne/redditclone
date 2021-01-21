<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\Subreddit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\User;
use ArrayObject;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{


    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * load data in the database
     *
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = Factory::create('fr_FR');

        $subreddits = new ArrayCollection();
        for($i=0;$i<3;$i++){
            $subreddit = new Subreddit();
            $subreddit->setTitle($faker->word());
            $subreddits->add($subreddit);
            $manager->persist($subreddit);
        }


        $users = new ArrayCollection();
        for ($i = 0;$i<10;$i++){
            $user = new User();
                $user->setEmail($faker->email)
                ->setUsername($faker->userName)
                ->setPassword($this->encoder->encodePassword($user, $faker->password(6, 8)));
            $users->add($user);
            $manager->persist($user);
        }

        $posts = new ArrayCollection();
        for ($i = 0;$i<20;$i++){
            $post = new Post();
                $post->setTitle($faker->sentence(6))
                ->setContent($faker->paragraph(3))
                ->setUser(($users->get(array_rand($users->toArray()))))
                ->setSubreddit($subreddits->get(array_rand($subreddits->toArray())))
                ->setCreatedAt($faker->dateTimeBetween('-1 years', 'now'));
            $posts->add($post);
            $manager->persist($post);
        }

        $comments = new ArrayCollection();
        for($i = 0;$i<150;$i++){
            $comment = new Comment();
            $comment->setContent($faker->sentence(12))->setPost($posts->get(array_rand($posts->toArray())))->setUser($users->get(array_rand($users->toArray())));
            $comments->add($comment);
            $manager->persist($comment);
        }


        for($i = 0;$i<150;$i++){
            $comment = new Comment();
            $randComment = $comments->get(array_rand($comments->toArray()));
            $user = $randComment->getUser();
            $post = $randComment->getPost();
            $comment->setContent($faker->sentence(12))->setPost($post)->setUser($user);
            $comment->setCommentParent($randComment);
            
            $manager->persist($comment);
        }

        $user = new User();
                $user->setEmail("admin@example.com")
                ->setUsername("admin")
                ->setPassword($this->encoder->encodePassword($user, "admin"))
                ->addRole('ROLE_ADMIN');
        $manager->persist($user);

        $manager->flush();
    }
}
