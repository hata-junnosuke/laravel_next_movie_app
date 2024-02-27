<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'content'=> 'required|string|max:200',
            'review_id'=> 'required|integer|exists:reviews,id'
        ]);

        $comment = Comment::create([
            'user_id'=> Auth::id(),
            'content'=> $validateData['content'],
            'review_id'=> $validateData['review_id']
        ]);
        // userリレーションをロードする
        $comment->load('user');

        return response()->json($comment);
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comment $comment)
    {
        if(Auth::id() !== $comment->user_id){
            return response()->json(["message" => "権限がありません。"], 401);
        }

        $validateData = $request->validate([
            'content'=> 'required|string'
        ]);

        $comment->update([
            "content" => $validateData["content"]
        ]);

        $comment->load("user");
        return response()->json($comment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        $comment->delete();
        return response()->json(["message"=> "無事に削除できました。"]);
    }
}