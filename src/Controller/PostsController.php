<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Post;
class PostsController extends AbstractController {

	private PostRepository $postRepository;

	public function __construct(PostRepository $postRepository) {
		$this->postRepository = $postRepository;
	}

	#[Route('/posts', name: 'blog_posts')]
	public function posts() {
		$posts = $this->postRepository->findAll();

		return $this->render('posts/index.html.twig', [
			'posts' => $posts
		]);
	}

	#[Route('/posts/{slug}', name:'blog_show')]
	public function post(Post $post) {
		return $this->render('posts/show.html.twig', [
			'post' => $post
		]);
	}

    #[Route('/posts', name: 'app_posts')]
    public function index(): Response {
        $posts = [
			'post_1' => [
			'title' => 'Заголовок первого поста',
			'body' => 'Тело первого поста'
		   ],
			'post_2' => [
			'title' => 'Заголовок второго поста',
			'body' => 'Тело второго поста'
		   ]
		 ];
		  return $this->render('posts/index.html.twig', [
			'posts' => $posts,
		]);
    }
}
