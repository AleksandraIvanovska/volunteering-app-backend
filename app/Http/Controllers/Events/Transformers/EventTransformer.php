<?php

namespace App\Http\Controllers\Events\Transformers;

use App\Events;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use League\Fractal\TransformerAbstract;


class EventTransformer extends TransformerAbstract
{
    /**
     * @param Events $model
     * @return array
     */
    public function transform(Events $model)
    {
        return [
            'uuid' => $model->uuid,
            'title' => $model->title,
            'description' => $model->description,
            'type' => $model->type,
            'is_route' => $model->is_route,
            'navigation_url' => $model->navigate_url,
            'is_read' => $model->pivot->is_read,
            'created_at' => $model->created_at,
         //   'image_url' => ($model->owner) ? $model->owner->contact->photo : null
        ];
    }

    public function latestEventsTransformer(Collection $model)
    {
        return $model->map(function ($event) {
            return [
                'uuid' => $event->uuid,
                'title' => $event->title,
                'description' => $event->description,
                'type' => $event->type,
                'is_route' => $event->is_route,
                'navigation_url' => $event->navigate_url,
                'is_read' => $event->pivot->is_read,
                'created_at' => Carbon::parse($event->created_at)->format('M d Y'),

              //  'image_url' => ($event->owner) ? $event->owner->contact->photo : null
            ];
        });
    }
}
