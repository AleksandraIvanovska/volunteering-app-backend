<?php

namespace App\Jobs\Emails;

use App\Volunteer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;


class VolunteerWasInvited implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    private $record, $authUser;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($record, $authUser)
    {
        $this->record = $record;
        $this->authUser = $authUser;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = Volunteer::where('id', $this->record['volunteer_id'])->with(['user'])->first();
      //  print_r(json_encode($user));
        $this->sendEmail($user);
      //  print_r($user->user['email']);
    }

    public function sendEmail($user) {
        try {

            $data = array(
                'name' => $user->user['name'],
                'mailMessage' => "You have been invited ....",
            );
            print_r($user->user['email']);

//            Mail::send('MailTemplate', $data, function ($message) use ($user) {
//
//                $message->from('noreply@aleksandra.com', 'Aleksandra Ivanovska');
//
//                $message->to($user->user['email'], $user->user['name'])->subject("SOME TEXT");
//
//            });
        }
        catch (\Exception $e) {
            print_r($e);
        }
    }
}
