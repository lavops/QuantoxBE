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
            'users.name',
            'users.imgURL'
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
        $comment->notify = true;

        $tweet->comment()->save($comment);

        return $this->getComments($tweet->id);
    }

    public function deleteComment(Request $request) {
        $user = auth()->user();
        $tweet = Tweet::Where('id',$request->tweet_id)->first();
        $comment = Comment::Where('id',$request->id)->first();
        $comment->delete();

        return $this->getComments($request->tweet_id);
    }
}
