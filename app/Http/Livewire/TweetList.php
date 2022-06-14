<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Services\UserService;
use Modules\Categories\app\Services\CategoryService;
use Modules\Tweets\app\Models\Tweet;
use Modules\Likes\app\Services\LikeService;
use Modules\Favourites\app\Services\FavouriteService;
use Modules\Tweets\app\Services\TweetService;

class TweetList extends Component
{
    public $content;
    public $tweet;
    public $perPageReplies = 3;

    protected $listeners = [
        'storeReply' => '$refresh',
        'perPageRepliesIncrease' => '$refresh'
    ];

    protected $rules = [
        'content' => 'required|max:280'
    ];

    public function updated($propertyName) 
    {
        $this->validateOnly($propertyName);
    }

    public function deleteTweet() 
    {
        Tweet::findOrFail($this->tweet->id)->delete();

        $this->emit('deleteTweet');
    }

    public function deleteReply(
        CategoryService $category, 
        FavouriteService $favourite, 
        $replyId
    ) 
    {
        Tweet::findOrFail($replyId)->delete();

        $this->tweet = $this->tweet->refresh();

        $this->tweet['category'] = $category->getCategoryById($this->tweet->category_id);
        $this->tweet['favourites'] = $favourite->getFavouriteByTweetAndUser($this->tweet->id);

        $this->emit('deleteReply');
    }

    public function storeReply(
        TweetService $service, 
        UserService $user, 
        CategoryService $category, 
        FavouriteService $favourite
    ) 
    {
        $this->validate();

        Tweet::create([
            'content' => $this->content,
            'tweet_id' => $this->tweet->id,
            'category_id' => $this->tweet->category->id ?? null,
            'user_id' => auth()->user()->id
        ]);

        if(!$user->isAuthor($this->tweet->user->id)) {
            $service->replyOnTweetNotification($this->tweet);
        }

        $this->tweet = $this->tweet->refresh();

        $this->tweet['category'] = $category->getCategoryById($this->tweet->category_id);
        $this->tweet['favourites'] = $favourite->getFavouriteByTweetAndUser($this->tweet->id);

        $this->reset('content');
    }

    public function likeTweet(
        LikeService $service, 
        CategoryService $category, 
        FavouriteService $favourite
    ) 
    {
        $service->likeUnlikeTweet($this->tweet);

        $this->tweet = $this->tweet->refresh();

        $this->tweet['category'] = $category->getCategoryById($this->tweet->category_id);
        $this->tweet['favourites'] = $favourite->getFavouriteByTweetAndUser($this->tweet->id);

        $this->emit('likeTweet');
    }

    public function addToFavourites(
        FavouriteService $service, 
        CategoryService $category, 
        FavouriteService $favourite
    ) 
    {
        $service->addFavourites($this->tweet->id);

        $this->tweet = $this->tweet->refresh();

        $this->tweet['category'] = $category->getCategoryById($this->tweet->category_id);
        $this->tweet['favourites'] = $favourite->getFavouriteByTweetAndUser($this->tweet->id);

        $this->emit('addToFavourites');
    }

    public function perPageRepliesIncrease() 
    {
        $this->perPageReplies += 3;
    }

    public function getRepliesProperty()
    {
        return Tweet::with('user')
            ->where('tweet_id', $this->tweet->id)
            ->orderBy('created_at', 'desc')
            ->cursorPaginate($this->perPageReplies);
    }

    public function render()
    {
        return view('livewire.tweet-list');
    }
}