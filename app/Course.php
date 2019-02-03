<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected  $fillable = ['common_courses_id', 'name', 'class'];

    public function teacher(){
        return $this->hasMany('App\Teacher');
    }

    public function student(){
        return $this->hasMany('App\Student');
    }

    public function assignment(){
        return $this->hasMany('App\Assignment');
    }

    public function common_course(){
        return $this->belongsTo('App\common_course');
    }

}
