<?php

namespace App\Repositories;

use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;

class PostRepository
{
    public function __construct(private Post $post) {}

    public function getAllPosts(): LengthAwarePaginator
    {
        $posts = $this->post->with('user')->paginate(20)->withQueryString();

        return $posts;
    }

    public function getPostById(int $id)
    {
        $post = $this->post->findOrFail($id);

        return $post;
    }

    public function insertNewPost(array $request): Post
    {
        $post = $this->post->create($request);

        return $post;
    }

    public function updatePostById(array $request, int $id)
    {
        $post = $this->post->findOrFail($id);
        $updatedPost = $post->update($request);

        return $updatedPost;
    }

    public function deletePostById(int $id): void
    {
        $post = $this->post->findOrFail($id);
        $post->delete();
    }
}
