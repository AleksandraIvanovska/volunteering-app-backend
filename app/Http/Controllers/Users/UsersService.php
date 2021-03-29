<?php


namespace App\Http\Controllers\Users;

use App\Support\HasRoleTrait;
use Laravel\Passport\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\User;
use Dingo\Api\Routing\Helpers;
use Laravel\Passport\TokenRepository;
use Illuminate\Events\Dispatcher;


class UsersService
{

    use Helpers, HasRoleTrait;

    private $dispatcher;

    public function __construct(Dispatcher $dispatcher, TokenRepository $tokenrepo, User $model)
    {
        $this->model = $model;
        $this->tokenRepo = $tokenrepo;
        $this->dispatcher = $dispatcher;
    }


    public function loginUser(Request $request){

        if (Auth::attempt($request->all())){
            $client=Client::where('user_id',Auth::user()->id)->firstorFail();

            $request->request->add([
                'grant_type' => 'password',
                'client_id' => $client->id,
                'client_secret' => $client->secret,
                'username' => $request->email,
                'password' => $request->password,
                'scope' => null
            ]);

            // Fire off the internal request.
            $proxy = Request::create(
                'oauth/token',
                'POST'
            );

            $response = \Route::dispatch($proxy);
            $responseToken = json_decode($response->getContent(), true);
            $cookie = cookie(
                'refresh_token',
                serialize(['rtk' => $responseToken['refresh_token'], 'cid' => Auth::user()->uuid]),
                864000, // 10 days
                null,
                null,
                false,
                true // HttpOnly
            );

            //save password to user table
            // Auth::user()->update(["remember_token" => $responseToken['access_token']]);

            return response([
                'access_token' => $responseToken['access_token']
            ])->cookie($cookie);
        }
        return response()->json(array('message' => 'Password is incorrect'), 404);
        }

    public function forgotPassword($request)
    {
        $user = $this->model->where('email', $request->get('email'))->firstOrFail();
        UserForgotPassword::dispatch($user);
        return $this->response->created();
    }

    public function isUserOrganization($request) {
        if ($this->isOrganization(Auth::user())) {
            return true;
        }
        else return false;
    }

    public function isUserVolunteer(Request $request) {
        if ($this->isVolunteer(Auth::user())) {
            return true;
        }
        else return false;
    }

}
