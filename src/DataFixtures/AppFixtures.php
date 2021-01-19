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

class AppFixtures extends Fixture
{
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
                ->setUsername($faker->word(1))
                ->setPassword($faker->password(6, 8));
            $users->add($user);
            $manager->persist($user);
        }

        $posts = new ArrayCollection();
        for ($i = 0;$i<20;$i++){
            $post = new Post();
                $post->setTitle($faker->sentence(6))
                ->setContent($faker->paragraph(3))
                ->setUser(($users->get(array_rand($users->toArray()))))
                ->setSubreddit($subreddits->get(array_rand($subreddits->toArray())));
            $posts->add($post);
            $manager->persist($post);
        }

        for($i = 0;$i<40;$i++){
            $comment = new Comment();
            $comment->setContent($faker->sentence(12))->setPost($posts->get(array_rand($posts->toArray())))->setUser($users->get(array_rand($users->toArray())));
            $manager->persist($comment);
        }


        $manager->flush();
    }
}
