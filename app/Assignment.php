<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    public $incrementing = false;

    protected $fillable = ['courses_id', 'content', 'percentage', 'name', 'start_date', 'start_time', 'end_date', 'end_time', 'announce_score'];

    public function teacher(){
        return $this->belongsTo('App\Teacher', 'corrector_id');
    }

    public function student(){
        return $this->belongsToMany('App\Student', 'student_assignment', 'assignments_id', 'students_id', 'id', 'users_id');
    }

    public function course(){
        return $this->belongsTo('App\Course', 'courses_id', 'id');
    }
}
