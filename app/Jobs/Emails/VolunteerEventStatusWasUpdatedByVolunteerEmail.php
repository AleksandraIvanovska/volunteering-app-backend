<?php

namespace App\Jobs\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class VolunteerEventStatusWasUpdatedByVolunteerEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;

    private $volunteering_event, $status, $owner, $user, $platform_url, $mailAddress, $platformName;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($volunteering_event, $status, $owner, $user)
    {
        $this->volunteering_event = $volunteering_event;
        $this->status = $status;
        $this->owner = $owner;
        $this->user = $user;
        $this->mailAddress = Config::get('mail.from.address');
        $this->platformName = Config::get('values.platform_name');
        $this->platform_url = Config::get('values.platform_url');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->sendEmail($this->user);
    }

    public function sendEmail($user) {
        try {
            $data = array(
                'name' => $user['name'],
                'mailMessage' => 'Volunteer <strong>' . $this->owner['name'] . '</strong> has changed their status on the event <strong>' . $this->volunteering_event->title . '</strong> to <strong>' . $this->status->description . '</strong>',
                'button' => "",
                'footer' => null
            );

            Mail::send('MailTemplate', $data, function ($message) use ($user) {
                $message->from($this->mailAddress, $this->owner->name);
                $message->to('aleksandraivanovska02@gmail.com', $user->name)->subject("Volunteer Request");
            });
        }
        catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
