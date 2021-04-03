<?php

namespace App\Jobs\Notifications;

use App\Support\EventNotificationTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class VolunteerEventsStatusWasUpdatedByVolunteer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, EventNotificationTrait;
    public $tries = 5;

    private $volunteering_event, $status, $owner, $user_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($volunteering_event, $status, $owner, $user_id)
    {
        $this->volunteering_event = $volunteering_event;
        $this->status = $status;
        $this->owner = $owner;
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $event = [
            'title' => 'Volunteer <strong>' . $this->owner['name'] . '</strong> has changed their status on the event <strong>' . $this->volunteering_event->title . '</strong> to <strong>' . $this->status->description . '</strong>',
            'description' => null,
            'navigate_url' => "/volunteeringEvents/" . $this->volunteering_event->uuid,
            'type' => 'user',
            'source_id' => $this->user_id,
            'source_table' => 'users',
            'sender_id' => $this->owner->id
        ];

        $this->insertEvent($event, $this->user_id);
    }
}
