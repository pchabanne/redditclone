<?php

namespace App\Controller;

use App\Repository\PostRepository;
use App\Service\PostsToArray;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class HomeController extends AbstractController
{
    private $postRepository;
    const FIRST = 0;
    const LIMIT = 8;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }
    /**
     * return the home page
     * @Route("/", name="home")
     * @param PostRepository $postRepository
     * @return Response
     */
    public function index(): Response
    {
        $user = $this->getUser();
        if(!$user){
            $posts = $this->postRepository->findAllOrderByDateAjax(self::FIRST,self::LIMIT);
            return $this->render('home/index.html.twig', [
                'posts' => $posts,
            ]);
        }

        $subreddits = $user->getSubreddits();
        if($subreddits->isEmpty()){
            $posts = $this->postRepository->findAllOrderByDateAjax(self::FIRST,self::LIMIT);
        }else{
            $posts = $this->postRepository->findAllInSubredditsOrderByDateAjax(self::FIRST,self::LIMIT, $subreddits);
        }

        return $this->render('home/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    /**
     * @Route("/get/posts", name="ajax_home_posts")
     *
     * @param PostRepository $postRepository
     * @param Request $request
     * @return Response
     */
    public function ajaxHomepage(Request $request, PostsToArray $postsToArray) :Response{
        $user = $this->getUser();
        $first = $request->query->get('first');
        $limit = $request->query->get('limit');
        if(!$user){
            $posts = $this->postRepository->findAllOrderByDateAjax($first, $limit);
        }else{
            $subreddits = $user->getSubreddits();
            if($subreddits->isEmpty()){
                $posts = $this->postRepository->findAllOrderByDateAjax($first,$limit);
            }else{
                $posts = $this->postRepository->findAllInSubredditsOrderByDateAjax($first,$limit, $subreddits);
            }
        }
        
        $postsJson = $postsToArray->postsToArray($posts);
        $response = new Response(json_encode($postsJson));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
