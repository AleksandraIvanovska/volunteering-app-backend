<?php

namespace App\Jobs\Notifications;

use App\Support\EventNotificationTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class VolunteerMadeRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, EventNotificationTrait;
    public $tries = 5;
    private $record, $owner, $volunteer, $user_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($record, $owner, $volunteer, $user_id)
    {
        $this->record = $record;
        $this->owner = $owner;
        $this->volunteer = $volunteer;
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
            'title' => 'Volunteer <strong>' . $this->owner->name . '</strong> has requested to volunteer on your event <strong>' . $this->record['title'] . '</strong>',
            'description' => null,
            'navigate_url' => "/volunteeringEvents/" . $this->record->uuid,
            'type' => 'user',
            //'is_route' => false,
            'source_id' => $this->user_id,
            'source_table' => 'users',
            'sender_id' => $this->owner->id
        ];

        $this->insertEvent($event, $this->user_id);
    }
}
