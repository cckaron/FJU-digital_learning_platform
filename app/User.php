<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account', 'name', 'email', 'password', 'type', 'id', 'remember_token',
        'created_at', 'updated_at',
        'last_login_at', 'last_login_ip'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function teacher(){
        return $this->hasOne('App\Teacher', 'users_id', 'id');
    }

    public function student(){
        return $this->hasOne('App\Student', 'users_id', 'id');
    }

    public function ta(){
        return $this->hasOne('App\Ta', 'users_id', 'id');
    }
}
