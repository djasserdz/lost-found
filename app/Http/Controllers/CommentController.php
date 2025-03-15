<?php

namespace App\Http\Controllers;

use App\Http\Resources\commentResource;
use App\Models\comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id)
    {

        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'message' => 'Post not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $validation = Validator::make($request->only('body', 'parent_id'), [
            'body' => ['required'],
            'parent_id' => ['nullable', 'exists:comments,id']
        ]);

        if ($validation->fails()) {
            return response()->json([
                'errors' => $validation->errors(),
            ]);
        }

        $user = Auth::user();


        $comment = $post->comments()->create([
            'body' => $request->body,
            'user_id' => $user->id,
            'parent_id' => $request->input('parent_id'),
        ]);

        return response()->json([
            'message' => 'Comment created!',
            'comment' => new commentResource($comment),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $comment = Comment::findOrFail($id);
        return response()->json($comment, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validation = Validator::make($request->only('body'), [
            'body' => ['required', 'string'],
        ]);

        if ($validation->fails()) {
            return response()->json(['error' => $validation->errors()], Response::HTTP_UNAUTHORIZED);
        }

        $comment = Comment::findOrFail($id);
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $comment->body = $request->input('body');
        $comment->save();

        return response()->json([
            'message' => 'Comment updated!',
            'comment' => $comment,
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $comment = Comment::findOrFail($id);

        if ($comment->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted!',
        ], Response::HTTP_OK);
    }
}
