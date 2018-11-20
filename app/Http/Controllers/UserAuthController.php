<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAuthController extends Controller
{
    public function signInPage(){
        return view('auth.signIn');
    }

    public function postSignIn(Request $request){
        $user_data = array(
            'id' => $request->input('id'),
            'password' => $request->input('password')
        );

        if (Auth::attempt($user_data)){
            return redirect()->route('dashboard.index');
        } else {
            return back()->with('error', 'Wrong Login Details');
        }
    }

    public function getSignOut(){
        Auth::logout();
        return redirect()->route('auth.signIn');
    }

}
