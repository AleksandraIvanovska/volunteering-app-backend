<?php

namespace App\Jobs\Notifications;

use App\Support\EventNotificationTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CommentForVolunteerWasMade implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, EventNotificationTrait;

    public $tries = 5;
    private $owner, $volunteer_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($owner, $volunteer_id)
    {
        $this->owner = $owner;
        $this->volunteer_id = $volunteer_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $event = [
            'title' => '<strong>' . $this->owner->name . '</strong> left you a comment. ',
            'description' => null,
            'navigate_url' => null,
            'type' => 'user',
            'source_id' => $this->volunteer_id,
            'source_table' => 'users',
            'sender_id' => $this->owner->id
        ];

        $this->insertEvent($event, $this->volunteer_id);
    }
}
