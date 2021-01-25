<?php

namespace App\Http\Controllers\Users;

use App\User;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;
use Laravel\Passport\ClientRepository;
use Illuminate\Support\Facades\Validator;


class UsersController extends Controller
{

    use Helpers;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    protected $model;
    protected $userService;
    private $dispatcher;

    /**
     * UsersController constructor.
     * @param Dispatcher $dispatcher
     * @param \App\User $model
     * @param UsersService $userService
     */
    public function __construct(Dispatcher $dispatcher, User $model, UsersService $userService)
    {
        $this->model = $model;
        $this->userService = $userService;
        $this->dispatcher = $dispatcher;
    }


    public function register(Request $request) {

        $validator=Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role_id' => 'required|exists:roles,id' //maybe change this later and send me role ex.volunteer/organization
        ]);

        if ($validator->fails()){
            return response()->json($validator->messages(),400);
        }

        $validateData = [
            'name' => $request->name,
            'role_id' => $request->role_id,
            'email' => $request->email,
            'password' => $request->password,
            'password_confirmation' => $request->password_confirmation,
            'remember_token' => $request->remember_token
        ];



        $validateData['password']=bcrypt($request['password']);

        $user=User::create($validateData);

        $accessToken=$user->createToken('authToken')->accessToken;

        return response(['user' => $user, 'access_token' => $accessToken]);
    }

    public function login(Request $request) {
        $validator=Validator::make($request->all(),[
            'email' => 'required|email|exists:users,email',
            'password' => 'required'
        ]);

        if ($validator->fails()){
            return response()->json($validator->messages(),400);
        }

//        return $this->userService->loginUser($request);

//        $loginData = $request->validate([
//            'email' => 'required|email',
//            'password' => 'required|min:8'
//        ]);
        $loginData = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        if (!auth()->attempt($loginData)){
            return response(['message' => 'Invalid credentials']);
        }

        $accessToken=auth()->user()->createToken('authToken')->accessToken;
        return response(['user' => auth()->user(), 'access_token' => $accessToken]);
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validator->fails())
            return response()->json($validator->messages(), 403);

        $forgotPassword = $this->userService->forgotPassword($request);
        return $forgotPassword;
    }


    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
