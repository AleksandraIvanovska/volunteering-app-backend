<?php


namespace App\Http\Controllers\Resources;

use App\Category;
use App\Cities;
use App\Countries;
use App\Language;
use App\LanguageLevel;
Use App\Resources;
use App\Roles;
use Dingo\Api\Routing\Helpers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class ResourcesService
{
    use Helpers;
    protected $model, $transformer;


    /**
     * ResourcesService constructor.
     */
    public function __construct(Resources $model)
    {
        $this->model = $model;
    }

    public function getResources(Request $request) {

        if ($request->has('type')) {
            $resources = $this->model->whereIn('type', explode(",", $request->input('type')))->get();
            return $resources;
        }

        return $this->model->get();
    }

    public function createResources(Request $request) {
        $this->model::create($request->all());
        return "Ok";
    }

    /**
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     */
    public function updateResources(Request $request)
    {
        return $this->response->noContent();
    }

    /**
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function deleteResource($id)
    {
        return $this->response->noContent();
    }

    public function getCountry(Request $request) {
        if($request->has(['name'])) {
            return Countries::where('name','like','%'. $request->input('name') . '%')->get();
        }
        else {
            return Countries::get();
        }
    }

    public function getCity(Request $request) {
        if ($request->has('name')) {
            return Cities::where('name','like','%' . $request->input('name') . '%')->get();
        }
        else {
            return "Need to add city name";
        }
    }

    public function getCategories(Request $request) {
        if($request->has('name')) {
            return Category::where('description', 'like', '%' . $request->input('name') . '%')->get();
        }
        else {
            return Category::get();
        }
    }

    public function getLanguages(Request $request) {
        if ($request->has('name')) {
            return Language::where('language','like','%' . $request->input('name') . '%')->get();
        }
        else {
            return Language::get();
        }
    }

    public function getLanguageLevels(Request $request)
    {
            return LanguageLevel::get();
    }

    public function getUserRoles()
    {
        return Roles::whereIn('name' ,['volunteer','organization'])->get();
    }

    public function getNationalities(Request $request) {
        if ($request->has('name')) {
            return Countries::where('nationality','like', '%' . $request->input('name') . '%')->select('uuid','nationality')->get();
        }
        else {
            return Countries::select('uuid','nationality')->get();
        }
    }

    public function getCountries(Request $request) {
        if ($request->has('name')) {
            return Countries::where('name','like','%' . $request->input('name') . '%')->get();
        }
        else {
            return Countries::get();
        }
    }

    public function getCities(Request $request) {
        if ($request->has('name')) {
            return Cities::where('name','like','%' . $request->input('name') . '%')->get();
        }
        else if ($request->has('state')) {
            return Cities::where('state_id', Countries::where('name','like' , '%' . $request->input('state') . '%')->value('id'))->get();
        }
        else {
            return Cities::get();
        }
    }

    public function getDurations(Request $request) {
        return Resources::where('type', 'duration_type')->get();
    }

    public function getGreatFor(Request $request) {
        return Resources::where('type', 'great_for_type')->get();
    }


}
