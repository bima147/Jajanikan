<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Socialite;
use App\Models\User;

class FacebookController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('facebook')->redirect();
    }
 
    public function callback()
    {
 
        // jika user masih login lempar ke home
        if (Auth::check()) {
            return redirect('/home');
        }
 
        $oauthUser = Socialite::driver('facebook')->stateless()->user();
        $user = User::where('facebook_id', $oauthUser->id)->first();
        if ($user) {
            Auth::loginUsingId($user->id);
            return redirect('/home');
        } else {
            $newUser = User::create([
                'nama' => $oauthUser->name,
                'alamat_email' => $oauthUser->email,
                'facebook_id'=> $oauthUser->id,
                // password tidak akan digunakan ;)
                'password' => md5($oauthUser->token),
            ]);
            Auth::login($newUser);
            return redirect('/home');
        }
    }
}
