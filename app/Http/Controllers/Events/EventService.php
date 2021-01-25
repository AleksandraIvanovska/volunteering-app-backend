<?php


namespace App\Http\Controllers\Events;

use App\User;
use App\Events;
use Carbon\Carbon;
use Dingo\Api\Routing\Helpers;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Auth;
use App\Support\EventNotificationTrait;
use App\Http\Controllers\Events\Transformers\EventTransformer;

class EventService
{

    use Helpers, EventNotificationTrait;
    private $dispatcher;
    protected $model, $transformer;


    public function __construct(Dispatcher $dispatcher, Events $model, EventTransformer $transformer)
    {
        $this->model = $model;
        $this->dispatcher = $dispatcher;
        $this->transformer = $transformer;
    }

    /**
     * @return array
     */
    public function getLatestEvents()
    {
        $latestEvents = User::where('id', Auth::user()->id)->with([
            'events' => function ($query) {
                $query->orderBy('created_at', 'desc')->take(10);
            },
//            'events.owner' => function ($query) {
//                $query->select('id');
//            }

//            'events.owner.contact' => function ($query) {
//                $query->select('id', 'photo');
            //}
            ])->withCount(['events' => function ($query) {
            $query->where('is_read', 0);
        }])->first();

        $notifications = array();
        $notifications['eventsCount'] = $latestEvents->events_count;
        $notifications['events'] = $this->transformer->latestEventsTransformer($latestEvents->events);;
        return $notifications;
    }


    public function getAllEvents()
    {
        $events = Auth::user()->load([
            'events' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
//            'events.owner' => function ($query) {
//                $query->select('id');
//            },
//            'events.owner.contact' => function ($query) {
//                $query->select('id', 'photo');
//            }
            ]);
        return $this->response->collection($events->events, $this->transformer);
    }

    public function markEventAsRead($uuid)
    {
        $this->model->byUuid($uuid)->firstOrFail()->users()->updateExistingPivot(Auth::user()->id, ['is_read' => true, 'read_time' => Carbon::now()]);
        return $this->response->created();
    }

    public function markAllEventAsRead()
    {
        $eventIds = Auth::user()->events()->allRelatedIds();
        foreach ($eventIds as $id){
            Auth::user()->events()->updateExistingPivot($id, ['is_read' => true, 'read_time' => Carbon::now()]);
        }
        return $this->response->created();
    }


    public function destroy($uuid)
    {
        return $this->response->noContent();
    }
}
