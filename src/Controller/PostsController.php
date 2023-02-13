<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Post;
use App\Form\PostType;
use Cocur\Slugify\Slugify;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

class PostsController extends AbstractController {

	private PostRepository $postRepository;
	private ManagerRegistry $doctrine;

	public function __construct(PostRepository $postRepository, ManagerRegistry $doctrine) {
		$this->postRepository = $postRepository;
		$this->doctrine = $doctrine;
	}

	#[Route('/posts', name: 'blog_posts')]
	public function posts() {
		$posts = $this->postRepository->findAll();

		return $this->render('posts/index.html.twig', [
			'posts' => $posts
		]);
	}

	#[Route('/posts/new', name: 'new_blog_post')]
	public function addPost(Request $request, Slugify $slugify) {
		$post = new Post();
		$form = $this->createForm(PostType::class, $post);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$post->setSlug($slugify->slugify($post->getTitle()));
			$post->setCreatedAt(new \DateTime());

			$em = $this->doctrine->getManager();
			$em->persist($post);
			$em->flush();

			return $this->redirectToRoute('blog_posts');
		}
		return $this->render('posts/new.html.twig', [
            'form' => $form->createView()
        ]);
	}

	#[Route('/posts/{slug}/edit', name:'blog_post_edit')]
	public function edit(Post $post, Request $request, Slugify $slugify) {
		$form = $this->createForm(PostType::class, $post);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$post->setSlug($slugify->slugify($post->getTitle()));
			$em = $this->doctrine->getManager();
			$em->flush();

			return $this->redirectToRoute('blog_show', [
				'slug' => $post->getSlug()
			]);
		}

		return $this->render('posts/new.html.twig', [
			'form' => $form->createView()
		]);
	}

	#[Route('/posts/{slug}/delete', name: 'blog_post_delete')]
	public function delete(Post $post) {
		$em = $this->doctrine->getManager();
		$em->remove($post);
		$em->flush();

		return $this->redirectToRoute('blog_posts');
	}

	#[Route('/posts/{slug}', name:'blog_show')]
	public function post(Post $post) {
		return $this->render('posts/show.html.twig', [
			'post' => $post
		]);
	}
}
