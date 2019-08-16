<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Http\Requests\CommentRequest;
use App\Tweet;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function getComments($id)
    {
        $user = auth()->user();

        $comments = Comment::join('users','comments.user_id','=','users.id')->select(
            'comments.*',
            'users.username',
            'users.name'
        )->Where('tweet_id', $id)->get();

        return $comments;
    }

    public function postComment(CommentRequest $request)
    {
        $user = auth()->user();

        $tweet = Tweet::Where('id',$request->tweet_id)->first();

        $comment = new Comment();
        $comment->user_id = $user->id;
        $comment->tweet_id = $request->tweet_id;
        $comment->text = $request->text;

        $tweet->comment()->save($comment);

        return $this->getComments($tweet->id);
    }
}
