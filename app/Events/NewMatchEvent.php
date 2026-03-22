<?php

namespace App\Events;

use App\Models\UserMatch;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMatchEvent
{
    use Dispatchable, SerializesModels;

    public UserMatch $match;
    public int $notifyUserId;

    public function __construct(UserMatch $match, int $notifyUserId)
    {
        $this->match = $match;
        $this->notifyUserId = $notifyUserId;
    }
}
