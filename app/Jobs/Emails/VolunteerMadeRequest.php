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

class VolunteerMadeRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;

    private $platform_url, $mailAddress, $platformName, $record, $owner, $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($record, $owner, $user)
    {
        $this->record = $record;
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
                'mailMessage' => 'Volunteer <strong>' . $this->owner->name . '</strong> has requested to volunteer on your event <strong>' . $this->record['title'] . '</strong>',
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
