<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class common_course extends Model
{
    protected  $fillable = ['id', 'name', 'year','semester','start_date', 'end_date'];

    public function course(){
        return $this->hasMany('App\Course');
    }
}
