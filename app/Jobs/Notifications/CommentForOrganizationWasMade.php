<?php

namespace App\Jobs\Notifications;

use App\Support\EventNotificationTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CommentForOrganizationWasMade implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, EventNotificationTrait;

    public $tries = 5;
    private $organization_id, $owner;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($owner, $organization_id)
    {
        $this->owner = $owner;
        $this->organization_id = $organization_id;
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
            'source_id' => $this->organization_id,
            'source_table' => 'users',
            'sender_id' => $this->owner->id
        ];

        $this->insertEvent($event, $this->organization_id);
    }
}
