<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Policies\PostPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response as FacadesResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return PostResource::collection(Post::paginate(5));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->only('title', 'description', 'image', 'categories'), [
            'title' => ['required'],
            'description' => ['required'],
            'image' => ['required', 'mimes:png,jpg,jpeg'],
            'categories' => ['required', 'array'],
            'categories.*.id' => ['required', 'exists:categories,id'],
        ]);

        if ($validation->fails()) {
            return response()->json([
                'error' => $validation->errors(),
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'You are not authenticated',
            ], Response::HTTP_NOT_FOUND);
        }


        $path = null;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('posts_images', 'public');
        }

        $post = $user->posts()->create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'image' => $path,
        ]);

        $categoryIds = collect($request->input('categories'))->pluck('id')->toArray();
        $post->categories()->attach($categoryIds);

        return response()->json([
            'message' => 'Post created!',
            'post' => new PostResource($post),
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json([
                'message' => 'Post not found',
            ], Response::HTTP_NOT_FOUND);
        }

        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $post = Post::find($id);
        if (!$post) {
            return response()->json([
                'message' => 'Post not found',

            ], Response::HTTP_NOT_FOUND);
        }
        /*if (!Gate::allows('update_post', $post)) {
            return response()->json([
                'message' => 'Unautherized',
            ], Response::HTTP_UNAUTHORIZED);
        }*/

        if ($request->user()->cannot('update', $post)) {
            return response()->json([
                'message' => 'Unautherized',
            ], Response::HTTP_UNAUTHORIZED);
        }
        dd('ta9dar');
        $validation = Validator::make($request->only('title', 'description', 'image', 'categories'), [
            'title' => ['required'],
            'description' => ['required'],
            'image' => ['required', 'image', 'mimes:png,jpg,jpeg'],
            'categories' => ['nullable', 'array'],
            'categories.*.id' => ['required', 'exists:categories,id'],
        ]);

        if ($validation->fails()) {
            return response()->json([
                'error' => $validation->errors(),
            ], Response::HTTP_UNAUTHORIZED);
        }




        $path = null;

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($post->image);
            $path = $request->image->store('public', 'posts_images');
            $post->image = $path;
        }

        $post->title = $request->input('title');
        $post->description = $request->input('description');
        $post->save();

        if ($request->has('categories')) {
            $categoryIds = collect($request->input('categories'))->pluck('id')->toArray();
            $post->categories()->syncWithoutDetaching($categoryIds);;
        }

        return response()->json([
            'message' => 'Post updated!',
            'Post' => new PostResource($post),
        ], Response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json([
                'message' => 'Post deleted',
            ], Response::HTTP_NOT_FOUND);
        }

        $post->categories()->detach();

        Storage::disk('public')->delete($post->image);

        $post->delete();

        return response()->json([
            'message' => 'Post deleted!'
        ], Response::HTTP_OK);
    }
}
