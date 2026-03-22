<?php

namespace App\Events;

use App\Models\UserMatch;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MatchCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly UserMatch $match)
    {
    }
}
