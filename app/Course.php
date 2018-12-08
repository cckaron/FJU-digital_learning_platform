<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected  $fillable = ['id', 'name', 'year','semester','start_date', 'end_date'];

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
