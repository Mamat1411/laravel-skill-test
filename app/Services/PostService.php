<?php

namespace App\Services;

use App\Models\Post;
use App\Repositories\PostRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class PostService
{
    public function __construct(private PostRepository $postRepository) {}

    public function getAllPosts(): LengthAwarePaginator
    {
        $posts = $this->postRepository->getAllPosts();

        return $posts;
    }

    public function getPostById(int $id)
    {
        $post = $this->postRepository->getPostById($id);

        return $post;
    }

    public function insertNewPost(array $request): Post
    {
        $post = $this->postRepository->insertNewPost($request);

        return $post;
    }

    public function updatePostById(array $request, int $id)
    {
        $post = $this->postRepository->updatePostById($request, $id);

        return $post;
    }

    public function deletePostById(int $id): void
    {
        $this->postRepository->deletePostById($id);
    }
}
