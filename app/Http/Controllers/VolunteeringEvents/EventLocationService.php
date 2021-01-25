<?php


namespace App\Http\Controllers\VolunteeringEvents;


use App\Cities;
use App\EventLocation;
use App\VolunteeringEvents;

class EventLocationService
{

    /**
     * EventLocationService constructor.
     */
    public function __construct(EventLocation $model)
    {
        $this->model = $model;
    }

    public function create($request) {
        $event_id = VolunteeringEvents::byUuid($request['event_uuid'])->value('id');
        $event_location = $this->model->create([
            'event_id' => $event_id,
            'location_id' => Cities::where('name', $request['city'])->value('id'),
            'address' => $request['address'],
            'show_map' => $request['show_map'],
            'longitude' => $request['longitude'],
            'latitude' => $request['latitude'],
            'postal_code' => $request['postal_code']
        ]);

        return $event_location;
    }

    public function update($request) {
        $event_location = $this->model->byUuid($request['uuid'])->first();

        if ($request->has('city')) {
            $event_location->update(['location_id' => Cities::where('name', $request['city'])->value('id')]);
        }

        if ($request->has('address')) {
            $event_location->update(['address' => $request['address']]);
        }

        if ($request->has('show_map')) {
            $event_location->update(['show_map' => $request['show_map']]);
        }

        if ($request->has('longitude')) {
            $event_location->update(['longitude' => $request['longitude']]);
        }

        if ($request->has('latitude')) {
            $event_location->update(['latitude' => $request['latitude']]);
        }

        if ($request->has('postal_code')) {
            $event_location->update(['postal_code' => $request['postal_code']]);
        }

        return $event_location;
    }

    public function destroy($request) {
        $event_location = $this->model->byUuid($request['uuid'])->first();
        $event_location->delete();
        return response()->noContent();
    }
}
