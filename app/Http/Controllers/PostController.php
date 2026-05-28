<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    public function __construct(private PostService $postService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = $this->postService->getAllPosts();
            $response = PostResource::collection($data);

            return response()->json([
                'status' => 200,
                'message' => 'success',
                'data' => $response,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['user_id'] = auth()->id();

        try {
            $post = DB::transaction(function () use ($validatedData) {
                return $this->postService->insertNewPost($validatedData);
            });

            return response()->json([
                'status' => 200,
                'message' => 'success',
                'data' => new PostResource($post),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        try {
            $post = $this->postService->getPostById($post->id);

            return response()->json([
                'status' => 200,
                'message' => 'success',
                'data' => new PostResource($post),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        $validatedData = $request->validated();

        try {
            $post = DB::transaction(function () use ($validatedData, $post) {
                return $this->postService->updatePostById($validatedData, $post->id);
            });

            return response()->json([
                'status' => 200,
                'message' => 'success',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        try {
            $this->postService->deletePostById($post->id);

            return response()->json([
                'status' => 200,
                'message' => 'success',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
