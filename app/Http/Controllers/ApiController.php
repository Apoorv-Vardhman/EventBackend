<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ApiController extends Controller
{
    public function login(Request $request)
    {
        $this->validate($request,[
            'email'=>['required'],
            'password'=>['required','string']
        ]);
        $user = User::where('email',$request->post('email'))->orWhere('mobile',$request->post('email'))->first();
        if($user)
        {
            /*check password*/
            if(Hash::check($request->post('password'),$user->password))
            {
                $token = $user->createToken('Application')->accessToken;
                $user->token = $token;
                return response()->json(array('message'=>'Login Successful','data'=>$user),200);
            }
            else {
                return response()->json(array('message'=>'Invalid Email Address or Password','data'=>null),401);
            }
        }
        else {
            return response()->json(array('message'=>'Invalid Email Address','data'=>null),401);
        }
    }
}
