<?php

namespace Modules\User\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserLogout
{
    use SerializesModels, Dispatchable;

    public $user;
    public $action='logout';

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        // return [];

        return new PrivateChannel('user-logout');
    }
}
