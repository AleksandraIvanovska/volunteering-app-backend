<?php

namespace App\Http\Controllers\Events;

use App\Http\Controllers\Events\EventService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;


class EventController extends Controller
{
    protected $eventService;

    /**
     * EventController constructor.
     * @param EventService $eventService
     */
    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    /**
     * @return array
     */
    public function getLatest()
    {
        return $this->eventService->getLatestEvents();
    }


    /**
     * @return mixed
     */
    public function getAll()
    {
        return $this->eventService->getAllEvents();
    }

    /**
     * @param $uuid
     * @return \Dingo\Api\Http\Response
     */
    public function markEventAsRead($uuid)
    {
        return $this->eventService->markEventAsRead($uuid);

    }

    /**
     * @return \Dingo\Api\Http\Response
     */
    public function markAllEventAsRead()
    {
        return $this->eventService->markAllEventAsRead();
    }
}
