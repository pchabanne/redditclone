<?php

namespace App\Controller;

use App\Repository\PostRepository;
use App\Repository\SubredditRepository;
use App\Repository\UserRepository;
use App\Service\PostsToArray;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    const FIRST = 0;
    const LIMIT = 8;
    private $postRepository;
    private $subredditRepository;
    private $userRepository;

    public function __construct(PostRepository $postRepository, SubredditRepository $subredditRepository, UserRepository $userRepository)
    {
        $this->postRepository = $postRepository;
        $this->subredditRepository = $subredditRepository;
        $this->userRepository = $userRepository;
    }
    
    /**
     * return the result of a search in the navbar
     * @Route("/search", name="search")
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $search = $request->query->get('search');
        $posts = $this->postRepository->search($search, self::FIRST, self::LIMIT);
        $subreddits = $this->subredditRepository->search($search, 0, 3);

        return $this->render('search/search.html.twig', [
            'search' => $search,
            'subreddits' =>$subreddits,
            'posts' => $posts,
        ]);
    }

    /**
     * return more posts when the user scroll on the search page
     * @Route("get/search", name="search.ajax")
     *
     * @param Request $request
     * @return Response
     */
    public function ajaxSearch(Request $request, PostsToArray $postsToArray): Response
    {
        $first = $request->query->get('first');
        $limit = $request->query->get('limit');
        $search = $request->query->get('search');
        $posts = $this->postRepository->search($search, $first, $limit);

        $postsJson = $postsToArray->postsToArray($posts);
        $response = new Response(json_encode($postsJson));

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
