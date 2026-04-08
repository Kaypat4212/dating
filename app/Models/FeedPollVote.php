<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedPollVote extends Model
{
    public $timestamps = false;

    protected $fillable = ['post_id', 'user_id', 'option_index'];

    protected function casts(): array
    {
        return ['option_index' => 'integer'];
    }

    public function post(): BelongsTo { return $this->belongsTo(FeedPost::class, 'post_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
