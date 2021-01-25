<?php

namespace App\Mail;

use App\Volunteer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VolunteerInvitation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($record, $authUser)
    {
        $this->record = $record;
        $this->authUser = $authUser;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $user = Volunteer::where('id', $this->record['volunteer_id'])->with(['user'])->first();
        return $this->from('aleksandraivanovska02@gmail.com')->subject(__('New subject'))
            ->view('MailTemplate');
    }
}
