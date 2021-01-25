<?php

namespace App\Http\Controllers\Resources;

use App\Resources;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;


class ResourcesController extends Controller
{

    protected $resourcesService;

    /**
     * ResourcesController constructor.
     */
    public function __construct(ResourcesService $resourcesService)
    {
        $this->resourcesService = $resourcesService;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->resourcesService->getResources($request);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'description' => 'required|string',
            'value' => 'required|string',
            'type' => 'required|string',
            'order' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }
        return $this->resourcesService->createResources($request);
    }

    public function update(Request $request)
    {
        return $this->resourcesService->updateResources($request);
    }

    public function destroy($id)
    {
        return $this->resourcesService->deleteResource($id);
    }

    public function getCountry(Request $request) {
        return $this->resourcesService->getCountry($request);
    }

    public function getCity(Request $request) {
        return $this->resourcesService->getCity($request);
    }

    public function getCategories(Request $request) {
        return $this->resourcesService->getCategories($request);
    }

    public function getLanguages(Request $request) {
        return $this->resourcesService->getLanguages($request);
    }

    public function getLanguageLevels(Request $request) {
        return $this->resourcesService->getLanguageLevels($request);
    }

    public function getUserRoles() {
        return $this->resourcesService->getUserRoles();
    }

    public function getNationalities(Request $request) {
        return $this->resourcesService->getNationalities($request);
    }



}
