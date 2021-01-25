<?php

namespace App\Http\Controllers\Assets;

use App\Asset;
use App\Http\Controllers\Controller;
use Dingo\Api\Routing\Helpers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class UploadFileController extends Controller
{
    use Helpers;

    protected $model;
    /**
     * UploadFileController constructor.
     */
    public function __construct(Asset $model)
    {
        $this->model = $model;
    }

    public function store(Request $request) {

    }

    public function updateAsset(Request $request, $uuid) {
        $request->merge(['asset_uuid' => $uuid]);

        $validator=Validator::make($request->all(),[
           'asset_uuid' => ['required', 'exists:assets,uuid',
               function ($attribute, $value, $fail) use ($request) {
                   if (Asset::byUuid($request['asset_uuid'])->exists() && !isset($request['asset_name'])) {
                       return $fail('The asset_name is required.');
                   }
               }]
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        $this->model->byUuid($request['asset_uuid'])->update(['asset_name' => $request['asset_name']]);
        return $this->response->created();

    }
}
