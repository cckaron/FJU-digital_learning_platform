<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = ['users_id', 'courses_id', 'users_name'];

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function course(){
        return $this->belongsTo('App\Course');
    }

    public function student(){
        return $this->belongsToMany('App\Student');
    }

    public function assignment(){
        return $this->hasMany('App\Assignment');
    }
}
