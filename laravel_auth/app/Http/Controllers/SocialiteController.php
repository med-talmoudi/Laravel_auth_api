<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirectToGoogle ()
    {
        return Socialite::driver("google")->redirect();
    }
    
    public function handelGoogleCallback()
    {
        $user = Socialite::driver('google')->user();
        $finduser = User::where('social_id', $user->id)->first();

        try {
            if($finduser){
                Auth::login($finduser);
                return redirect('/home');
            }else{
                $new_user = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'social_id' => $user->id,
                    'social_type' => 'google',
                    'password' => Hash::make('google-auth'),

                ]);
                Auth::login($new_user);
                return redirect('/home');
            }
        } catch (Exception $e) {
            dd($e->getMessage());
           
        }
    }
}
