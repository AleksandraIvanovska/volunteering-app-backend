<?php

namespace App\Support;

use App\Events;


/**
 * Class HasPermissionsUuid.
 */
trait EventNotificationTrait
{

    public function insertEvent($eventRecord, $users)
    {
        $event = Events::create($eventRecord);
        $event->users()->attach($users);
    }
}
