<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileView extends Model
{
    public $timestamps = false;
    protected $fillable = ['viewer_id', 'viewed_id'];
    protected function casts(): array { return ['viewed_at' => 'datetime']; }

    public function viewer(): BelongsTo { return $this->belongsTo(User::class, 'viewer_id'); }
    public function viewed(): BelongsTo { return $this->belongsTo(User::class, 'viewed_id'); }
}
