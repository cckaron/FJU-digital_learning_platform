<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class student_assignment extends Model
{
    protected $fillable = ['students_id', 'score', 'status'];

    public function file(){
        return $this->hasMany('App\File');
    }
}
