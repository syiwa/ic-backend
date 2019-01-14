<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use Hash;

class UserController extends Controller
{
    /**
     * Login check.
     * @param  Request $request [description]
     * @return JSON             [description]
     */
    public function login(\App\Http\Requests\LoginRequest $request)
    {
    	if(Auth::attempt(request(['email','password']))){
    		$user = Auth::user();

            $user->load('roles');

    		$tokenResult = $user->createToken('ICSG');

    		$token = $tokenResult->token;

    		if($request->remember_me){
    			$token->expires_at = Carbon::now()->addWeeks(1);

    			$token->save();
    		}

    		return jsonResponse([
    			'access_token' => $tokenResult->accessToken,
    			'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
                'user' => $user->toArray()
    		]);
    	}else{
            return jsonResponse([
                'message' => 'The email and password are didn\'t match in out database.'
            ],422);
        }
    }

    /**
     * Logout user.
     * @return Boolean [description]
     */
    public function logout()
    {
        if (Auth::check()) {
            Auth::user()->AauthAcessToken()->delete();
        }

        return jsonResponse([
            'status' => true
        ]);
    }

    /**
     * List of Users
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function index(Request $request)
    {
        $users = \App\User::paginate()->appends($request->all());

        return jsonResponse($users->toArray());
    }

    /**
     * User detail
     * @param  Request $request [description]
     * @param  [type]  $id      [description]
     * @return [type]           [description]
     */
    public function show(Request $request,$id)
    {
        if(Auth::user()->hasRole('user') && Auth::user()->id != $id){
            return jsonResponse([
                "message" => "Unauthorized."
            ],403);
        }

        return jsonResponse(\App\User::find($id)->toArray());
    }

    /**
     * Make new User
     * @param  \App\Http\Requests\UserRequest $request [description]
     * @return [type]                                  [description]
     */
    public function store(\App\Http\Requests\UserRequest $request)
    {
        $user = \App\User::create(
            array_merge($request->all(), [
                'password' => bcrypt('password')
            ])
        )->assignRole('user');

        return jsonResponse($user->toArray());
    }

    /**
     * Update User
     * @param  \App\Http\Requests\UserRequest $request [description]
     * @return [type]                                  [description]
     */
    public function update(\App\Http\Requests\UserRequest $request, $id)
    {
        $data = array_filter($request->only(['name','email','phone','address']),function($value){
            return $value != null && $value != "";
        });

        if(Auth::user()->hasRole('user')){
            if(Auth::user()->id != $id){
                return jsonResponse([
                    "message" => "Unauthorized."
                ],403);
            }

            $data = $request->only(['phone']);
        }

        $user = \App\User::findOrFail($id);

        $user->fill($data);

        $user->save();

        return jsonResponse($user->toArray());
    }

    /**
     * Delete User
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function destroy($id)
    {
        if(Auth::user()->id == $id) {
            return jsonResponse([
                "message" => "Unauthorized."
            ],403);
        }

        return jsonResponse([
            'status' => \App\User::destroy($id)
        ]);
    }

    /**
     * Change password
     * @param  \App\Http\Requests\PasswordRequest $request [description]
     * @return [type]                                      [description]
     */
    public function changePassword(\App\Http\Requests\PasswordRequest $request)
    {
        $user = Auth::user();

        if(!Hash::check($request->old_password, $user->password)){
            return jsonResponse([
                "message" => "Wrong Password."
            ],422);
        }

        $user->password = bcrypt($request->new_password);

        return jsonResponse([
            "status" => $user->save()
        ]);
    }
}
