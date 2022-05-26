<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\TweetRequest;
use App\Models\Tweet;
use App\Services\TweetService;
use Illuminate\Http\Request;

class TweetController extends Controller
{
    protected Request $request;
    protected TweetService $service;

    public function __construct(Request $request, TweetService $service)
    {
        $this->request = $request;
        $this->service = $service;
    }

    public function tweets()
    {
        return Tweet::where('user_id', $this->request->user()->id)->cursorPaginate();
    }

    public function store(TweetRequest $request)
    {
        return $this->service->createTweet($request);
    }

    public function get($tweet_id) 
    {
        return $this->service->getUserTweet($tweet_id);
    }

    public function replies($tweet_id) 
    {
        return $this->service->getTweetReplies($tweet_id)->cursorPaginate();
    }

    public function like($tweet_id) 
    {
        return $this->service->likeTweet($tweet_id);
    }

    public function unlike($tweet_id) 
    {
        return $this->service->unlikeTweet($tweet_id);
    }

    public function reply($tweet_id) 
    {
        return $this->service->replyTweet($tweet_id);
    }
}
