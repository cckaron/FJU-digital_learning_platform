<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class student_assignment extends Model
{
    public $incrementing = false;

    protected $fillable = ['students_id', 'score', 'status', 'remark', 'comment'];

    public function file(){
        return $this->hasMany('App\File');
    }
}
