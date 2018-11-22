<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = ['courses_id', 'name', 'start_date', 'end_date'];

    public function teacher(){
        return $this->belongsTo('App\Teacher', 'corrector_id');
    }

    public function student(){
        return $this->belongsTo('App\Teacher', 'student_id');
    }

    public function course(){
        return $this->belongsTo('App\Course');
    }
}
