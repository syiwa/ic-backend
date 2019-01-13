<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;

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
                'message' => 'wrongLogin'
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
        return \App\User::paginate()->appends($request->all());
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

        return \App\User::find($id);
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
        $data = $request->only(['name','email','phone','address']);

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
}
