<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchQuestionAnswer extends Model
{
    protected $fillable = ['match_id', 'user_id', 'question_id', 'answer', 'answered_date'];

    protected function casts(): array
    {
        return ['answered_date' => 'date'];
    }

    public function match(): BelongsTo    { return $this->belongsTo(UserMatch::class, 'match_id'); }
    public function user(): BelongsTo     { return $this->belongsTo(User::class); }
    public function question(): BelongsTo { return $this->belongsTo(MatchQuestion::class, 'question_id'); }
}
