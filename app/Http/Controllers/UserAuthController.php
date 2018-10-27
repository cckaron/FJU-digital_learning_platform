<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
class UserAuthController extends Controller
{
    public function signInPage(){
        return view('auth.signIn');
    }
}