<?php

namespace CSlant\LaravelLike\Tests\FakeModels;

use CSlant\LaravelLike\HasLike;
use CSlant\LaravelLike\HasLove;
use Illuminate\Database\Eloquent\Model;

class FakePost extends Model
{
    use HasLike;
    use HasLove;

    protected $table = 'posts';
    protected $guarded = [];

    public function like($user)
    {
        // Giả lập hành động like
        return $this->likes()->create(['user_id' => $user->id]);
    }
}
