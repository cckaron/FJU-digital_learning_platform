<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class file extends Model
{
    public function student_assignment(){
        return $this->belongsTo('App\student_assignment');
    }
}
