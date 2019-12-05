<?php

namespace App\Http\Controllers;

use App\Teacher;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function getUpdateProfile(){
        $user = Auth::user();
        $occupation = '';

        //學生
        if($user->type == 4){
            $occupation = $user->student()->first()->occupation;
        }

        return view('profile.updateProfile', [
            'user' => $user,
            'occupation' => $occupation
        ]);
    }

    public function postUpdateProfile(Request $request){
        $user = Auth::user();
        $user_id = (string)$user->id;
        $user_type = $user->type;

        Validator::make($request->all(), [
            "email" => ['required', Rule::unique('users')->ignore($user_id)],
            "phone" => ['required', Rule::unique('users')->ignore($user_id)],
            "password"    => [
                'required',
                'array',
                'min:2',
                function($attribute, $value, $fail) {
                    if ($value[0] != $value[1]){
                        return $fail('新密碼 與 確認新密碼 不相同');
                    }
                },
            ],
            "password.*"  => "required|string|min:6",
        ])->validate();


//        $indexController = new DashboardController();
        $password = $request->get('password');
        $password = $password[0];
        $email = $request->get('email');
        $phone = $request->get('phone');
        $occupation = $request->get('occupation');

        //update database data
        if ($user_type == 3){ //teacher
            DB::table('users')
                ->where('id', sprintf("%06d", $user_id))
                ->update([
                    'password' => bcrypt($password),
                    'email' => $email,
                    'phone' => $phone,
                ]);

            DB::table('teachers')
                ->where('users_id', sprintf("%06d", $user_id)) //自動補0
                ->update([
                    'profileUpdated' => true,
                ]);
//            return $indexController->getTeacherIndex();
            return redirect()->route('dashboard.get');

        } else { //student
            if ($request->get('agreement') != null){
                $agreement = true;
            } else {
                $agreement = false;
            }

            DB::table('users')
                ->where('id', $user_id)
                ->update([
                    'password' => bcrypt($password),
                    'email' => $email,
                    'phone' => $phone,
                ]);

            DB::table('students')
                ->where('users_id', $user_id)
                ->update([
                    'occupation' => $occupation,
                    'profileUpdated' => true,
                    'agreement' => $agreement
                ]);

            return redirect()->route('index.student')->with('message', '個人檔案設定完成!');
        }
    }
}
