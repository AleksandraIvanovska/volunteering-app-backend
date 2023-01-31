<?php


namespace App\Http\Controllers\VolunteeringEvents;


use App\EventRequirements;
use App\VolunteeringEvents;

class EventRequirementsService
{


    /**
     * EventRequirementsService constructor.
     */
    public function __construct(EventRequirements $model)
    {
        $this->model = $model;
    }

    public function create($request) {
        $event = VolunteeringEvents::byUuid($request['event_uuid'])->first();
        $requirements = $this->model->create([
            'event_id' => $event->id,
            'driving_license' => isset($request['driving_license']) ? $request['driving_license'] : null,
            'minimum_age' => isset($request['minimum_age']) ? $request['minimum_age'] : null,
//            'languages' => isset($request['languages']) ? json_encode($request['languages']) : null,
            'languages' => isset($request['languages']) ? $request['languages'] : null,
            'orientation' => isset($request['orientation']) ? $request['orientation'] : null,
            'background_check' => isset($request['background_check']) ? $request['background_check'] : null,
            'other' => isset($request['other']) ? $request['other'] : null
        ]);

        return [
            "message" => "Event requirements has been successfully updated"
        ];

        return $requirements;
    }

    public function update($request) {
        $requirements = EventRequirements::byUuid($request['uuid'])->first();
        if ($request->has('driving_license')) {
            $requirements->update(['driving_license' => $request['driving_license']]);
        }

        if ($request->has('minimum_age')) {
            $requirements->update(['minimum_age' => $request['minimum_age']]);
        }

        if ($request->has('languages')) {
            $requirements->update(['languages' => $request['languages']]);
        }

        if ($request->has('orientation')) {
            $requirements->update(['orientation' => $request['orientation']]);
        }

        if ($request->has('background_check')) {
            $requirements->update(['background_check' => $request['background_check']]);
        }

        if ($request->has('other')) {
            $requirements->update(['other' => $request['other']]);
        }

        return [
            "message" => "Event requirements has been successfully updated"
        ];
        return $requirements;
    }

    public function destroy($request) {
        $requirements = $this->model->byUuid($request['uuid'])->first();
        $requirements->delete();
        return response()->noContent();
    }

}
