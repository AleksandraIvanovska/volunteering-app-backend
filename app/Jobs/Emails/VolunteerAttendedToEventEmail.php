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

class VolunteerAttendedToEventEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;

    private $platform_url, $mailAddress, $platformName, $record, $authUser, $volunteer;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($record, $authUser, $volunteer)
    {
        $this->record = $record;
        $this->authUser = $authUser;
        $this->volunteer = $volunteer;
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
        $user = $this->volunteer->user;
        $this->sendEmail($user);
    }

    public function sendEmail($user) {
        try {
            $data = array(
                'name' => $user['name'],
                'mailMessage' => "Congratulations! You have attended and finished your work on the volunteering event <strong>{$this->record->title}</strong> organized by organization <strong>{$this->authUser->name}</strong>",
                'button' => "",
                'footer' => null
            );

            Mail::send('MailTemplate', $data, function ($message) use ($user) {
                $message->from($this->mailAddress, $this->authUser->name);
                $message->to('aleksandraivanovska02@gmail.com', $user->name)->subject("Volunteer Request");
            });
        }
        catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
