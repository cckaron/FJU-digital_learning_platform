<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected  $fillable = ['common_courses_id, name'];

    public function teacher(){
        return $this->hasMany('App\Teacher');
    }

    public function student(){
        return $this->hasMany('App\Student');
    }

    public function assignment(){
        return $this->hasMany('App\Assignment');
    }

}
