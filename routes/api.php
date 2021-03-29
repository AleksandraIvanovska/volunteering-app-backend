<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
    $api->group(['middleware' => ['throttle:10000,1', 'bindings'], 'namespace' => 'App\Http\Controllers'], function ($api) {

        $api->get('getEverything', 'Backend\BackendController@index');

        $api->get('hello', function () {
            return ['Fruits' => 'Delicious and healthy!'];
        });

        $api->group(['prefix' => 'users'], function ($api) {
            $api->post('/register', 'Users\UsersController@register');
            $api->post('/login', 'Users\UsersController@login');
        });

        $api->group(['middleware' => ['auth:api']], function ($api) {
            $api->get('isAuth', function () {
                return ['Fruits' => 'Delicious and healthy!'];
            });

            $api->group(['prefix' => 'resources'], function ($api) {
                $api->get('/', 'Resources\ResourcesController@index');
                $api->post('/', 'Resources\ResourcesController@create');
                $api->get('/country', 'Resources\ResourcesController@getCountry');
                $api->get('/city', 'Resources\ResourcesController@getCity');
                $api->get('/categories', 'Resources\ResourcesController@getCategories');
                $api->get('/languages', 'Resources\ResourcesController@getLanguages');
                $api->get('/languageLevels','Resources\ResourcesController@getLanguageLevels');
                $api->get('/userRoles', 'Resources\ResourcesController@getUserRoles');
                $api->get('/nationalities', 'Resources\ResourcesController@getNationalities');
                $api->get('/countries', 'Resources\ResourcesController@getCountries');
                $api->get('/cities', 'Resources\ResourcesController@getCities');
                $api->get('/durations', 'Resources\ResourcesController@getDurations');
                $api->get('/greatFor', 'Resources\ResourcesController@getGreatFor');
            });

            $api->group(['prefix' => 'events'], function ($api) {
                $api->get('/latest', 'Events\EventController@getLatest');
                $api->get('/markAllAsRead', 'Events\EventController@markAllEventAsRead');
                $api->get('/{uuid}/markAsRead', 'Events\EventController@markEventAsRead');
                $api->get('/', 'Events\EventController@getAll');
            });

            $api->group(['prefix' => 'organizations'], function ($api) {
                $api->get('/','Organizations\OrganizationsController@index');
                $api->get('/{uuid}','Organizations\OrganizationsController@getByUuid');
                $api->post('/','Organizations\OrganizationsController@create');
                $api->put('/{uuid}','Organizations\OrganizationsController@update');
                $api->delete('/{uuid}','Organizations\OrganizationsController@destroy');
                $api->get('/{uuid}/contacts','Organizations\OrganizationsController@getOrganizationContacts');

                $api->group(['prefix' => '/{uuid}/assets'], function ($api) {
                    //$api->get('/', 'Organizations\OrganizationsController@getAllAssets');
                    //$api->get('/{organization_asset_uuid}', 'Organizations\OrganizationsController@getAssetByUuid');
                    $api->post('/','Organizations\OrganizationsController@createOrganizationAsset');
                    //$api->put('/{organization_asset_uuid}', 'Organizations\OrganizationsController@updateOrganizationAsset');
                    $api->delete('/{organization_asset_uuid}', 'Organizations\OrganizationsController@deleteOrganizationAsset');
                });

                $api->group(['prefix' => 'contact'], function ($api) {
                    $api->post('/','Contacts\ContactsController@create');
                    $api->put('/{uuid}', 'Contacts\ContactsController@update');
                    $api->delete('/{uuid}','Contacts\ContactsController@destroy');
                });

                $api->group(['prefix' => 'comment'], function ($api) {
                    $api->post('/','Organizations\OrganizationsController@createComment');
                    $api->put('/{uuid}','Organizations\OrganizationsController@updateComment');
                    $api->delete('/{uuid}','Organizations\OrganizationsController@deleteComment');
                });
            });

                //call this before organizationAsset or volunteerAssets
            $api->group(['prefix' => 'assets'], function ($api) {
                $api->post('/', 'Assets\UploadFileController@store');
                $api->put('/{uuid}', 'Assets\UploadFileController@updateAsset');
            });

            $api->group(['prefix' => 'volunteers'], function ($api) {
                $api->get('/','Volunteers\VolunteersController@index');
                $api->get('/{uuid}','Volunteers\VolunteersController@getByUuid');
                $api->post('/','Volunteers\VolunteersController@create');
                $api->put('/{uuid}','Volunteers\VolunteersController@update');
                $api->delete('/{uuid}', 'Volunteers\VolunteersController@destroy');

                $api->group(['prefix' => 'education'], function ($api) {
                    //$api->get('/','Volunteers\VolunteerEducationController@index');
                    //$api->get('/{uuid}','Volunteers\VolunteerEducationController@getByUuid');
                    $api->post('/','Volunteers\VolunteerEducationController@create');
                    $api->put('/{uuid}', 'Volunteers\VolunteerEducationController@update');
                    $api->delete('/{uuid}','Volunteers\VolunteerEducationController@destroy');
                });

                $api->group(['prefix' => 'experience'], function ($api) {
                    //$api->get('/','Volunteers\VolunteerExperienceController@index');
                    //$api->get('/{uuid}','Volunteers\VolunteerExperienceController@getByUuid');
                    $api->post('/','Volunteers\VolunteerExperienceController@create');
                    $api->put('/{uuid}', 'Volunteers\VolunteerExperienceController@update');
                    $api->delete('/{uuid}','Volunteers\VolunteerExperienceController@destroy');
                });

                $api->group(['prefix' => 'language'], function ($api) {
                   $api->post('/', 'Volunteers\VolunteersController@createVolunteerLanguage');
                   $api->put('/{uuid}', 'Volunteers\VolunteersController@updateVolunteerLanguage');
                   $api->delete('/{uuid}', 'Volunteers\VolunteersController@deleteVolunteerLanguage');
                });

                $api->group(['prefix' => 'favoriteOrganization'], function ($api) {
                    $api->post('/', 'Volunteers\VolunteersController@createFavoriteOrganization');
                    //$api->put('/{uuid}', 'Volunteers\VolunteersController@updateFavoriteOrganization');
                    $api->delete('/{uuid}', 'Volunteers\VolunteersController@deleteFavoriteOrganization');
                });

                $api->group(['prefix' => 'favoriteEvent'], function ($api) {
                   $api->post('/', 'Volunteers\VolunteersController@createFavoriteEvent');
                   $api->delete('/{uuid}', 'Volunteers\VolunteersController@deleteFavoriteEvent');
                });

                $api->group(['prefix' => 'comment'], function ($api) {
                   $api->post('/','Volunteers\VolunteersController@createComment');
                   $api->put('/{uuid}','Volunteers\VolunteersController@updateComment');
                   $api->delete('/{uuid}','Volunteers\VolunteersController@deleteComment');
                });



            });

            $api->group(['prefix' => 'volunteeringEvents'], function ($api) {
                $api->get('/', 'VolunteeringEvents\VolunteeringEventsController@index');
                $api->get('/{uuid}', 'VolunteeringEvents\VolunteeringEventsController@getByUuid');
                $api->post('/', 'VolunteeringEvents\VolunteeringEventsController@create');
                $api->put('/{uuid}', 'VolunteeringEvents\VolunteeringEventsController@update');
                $api->delete('/{uuid}', 'VolunteeringEvents\VolunteeringEventsController@destroy');

                $api->group(['prefix' => '/{uuid}/assets'], function ($api){
                   $api->post('/', 'VolunteeringEvents\VolunteeringEventsController@createEventAsset');
                   $api->delete('/{event_asset_uuid}', 'VolunteeringEvents\VolunteeringEventsController@deleteEventAsset');
                });

                $api->group(['prefix' => '/{uuid}/contacts'], function ($api) {
                   $api->post('/', 'VolunteeringEvents\VolunteeringEventsController@createEventContact');
                   $api->delete('/{event_contact_uuid}', 'VolunteeringEvents\VolunteeringEventsController@deleteEventContact');
                });

                $api->group(['prefix' => 'requirements'], function ($api) {
                    $api->post('/', 'VolunteeringEvents\EventRequirementsController@create');
                    $api->put('/{uuid}', 'VolunteeringEvents\EventRequirementsController@update');
                    $api->delete('/{uuid}', 'VolunteeringEvents\EventRequirementsController@destroy');
                });

                $api->group(['prefix' => 'location'], function ($api) {
                    $api->post('/', 'VolunteeringEvents\EventLocationController@create');
                    $api->put('/{uuid}', 'VolunteeringEvents\EventLocationController@update');
                    $api->delete('/{uuid}', 'VolunteeringEvents\EventLocationController@destroy');
                });

                //put this in volunteering events controller
                $api->group(['prefix' => 'eventAttendance'], function ($api) {
                    $api->post('/','VolunteeringEvents\VolunteeringEventsController@createVolunteerAttendance');
                    $api->delete('/{uuid}', 'VolunteeringEvents\VolunteeringEventsController@deleteVolunteerAttendance');
                });

                $api->group(['prefix' => 'volunteerInvitation'], function ($api) {
                    $api->post('/','VolunteeringEvents\VolunteeringEventsController@createVolunteerInvitation');
                    $api->put('/{uuid}','VolunteeringEvents\VolunteeringEventsController@updateVolunteerInvitation');
                    $api->delete('/{uuid}', 'VolunteeringEvents\VolunteeringEventsController@deleteVolunteerInvitation');
                });

            });

            $api->group(['prefix' => 'users'], function ($api) {
                $api->get('/isUserOrganization', 'Users\UsersController@isUserOrganization');
                $api->get('/isUserVolunteer', 'Users\UsersController@isUserVolunteer');
            });

        });

    });
});
