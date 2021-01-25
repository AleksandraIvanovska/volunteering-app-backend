<?php

namespace App\Jobs\Notifications;

use App\Support\EventNotificationTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class VolunteerWasInvited implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, EventNotificationTrait;

    public $tries = 5;

    private $record, $owner, $volunteer;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($record, $owner, $volunteer)
    {
        $this->record = $record;
        $this->owner = $owner;
        $this->volunteer = $volunteer;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $event = [
            'title' => 'You have been invited to the volunteering event <strong>' . $this->record['title'] . '</strong> by <strong>' . $this->owner->name . '</strong>',
            'description' => null,
            'navigate_url' => "/volunteeringEvents/" . $this->record->uuid,
            'type' => 'user',
      //      'is_route' => false,
            'source_id' => $this->volunteer->user->id,
            'source_table' => 'users',
            'sender_id' => $this->owner->id
        ];

        $this->insertEvent($event, $this->volunteer->user->id);
    }
}
