<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(CommentRequest $request)
    {
        $comment = new Comment([
            'content' => $request->get('content'),
            'parent_id' => $request->get('parent_id', null)
        ]);

        $comment->user()->associate(Auth::user());
        $comment->article()->associate($request->get('article_id'));
        $comment->save();
        $article = Article::query()->find($request->get('article_id'));
        $article->increment('reply_count');
        return $this->success('成功');
    }
}
