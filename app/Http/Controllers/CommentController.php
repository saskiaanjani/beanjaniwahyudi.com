<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;


class CommentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'article_id' => 'required|exists:articles,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'body' => 'required|string',
        ]);

        Comment::create([
            'article_id' => $request->article_id,
            'name' => $request->name,
            'email' => $request->email,
            'body' => $request->body,
        ]);

        return response()->json(['message' => 'Komentar berhasil disimpan!']);
    }

    public function index($articleId)
    {
        // Pastikan $articleId yang diterima valid
        if (!is_numeric($articleId)) {
            return response()->json(['message' => 'Invalid article ID'], 400);
        }
    
        try {
            $comments = Comment::where('article_id', $articleId)->get();
            return response()->json($comments);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Internal server error'], 500);
        }
    }

}
