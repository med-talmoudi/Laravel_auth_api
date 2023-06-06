<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Exception ;
use Illuminate\Support\Facades\Hash;




class AuthController extends Controller
{
   
    public function login(Request $request){
        $validator = Validator::make($request->all(),[
            'email'=>'required|email',
            'password'=>'required|min:8'
        ]);
        if ($validator-> failed()) {
            return sendError('validation Errors :',$validator->errors(),422);
        }

        $credentials = $request->only('email','password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $success['name']= $user->name;
          

            $success['token']= $user->createToken('laravelauth')->accessToken;

            return sendResponse($success,'You are logged successfully');
        }else {
            return sendError('Unauthorized',['error'=>'Unauthorized'],401);
        }
    }
    public function register(Request $request){
        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required|email',
            'password'=>'required|min:8'
        ]);
        if ($validator-> failed()) {
            return sendError('validation Errors :',$validator->errors(),422);
        }

        try {
            $user = User::create([
                'name'=> $request->name,
                'email'=> $request->email,
                'password'=> Hash::make($request->password),
            ]);

            $success['name']=$user->name;
            $message = 'Your Account created successfully';
            $success['token']= $user->createToken('laravelauth')->accessToken;

        } catch (Exception $e) {
            $success['token' ] = '';
            $message = 'Failed to create account!';
        }
        return sendResponse($success,$message);
    }
    public function userDetails()
    {
       $user = Auth::user();
       return sendResponse($user,'User Profile');
    }
}


