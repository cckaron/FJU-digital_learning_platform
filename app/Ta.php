<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ta extends Model
{
    protected $fillable = ['users_id', 'courses_id', 'users_name', 'department', 'grade', 'class', 'remark', 'created_at', 'updated_at'];

    public function user(){
        return $this->belongsTo('App\User', 'users_id', 'id');
    }

    public function course(){
        return $this
            ->belongsToMany('App\Course', 'ta_course', 'tas_id', 'courses_id', 'users_id', 'id');
    }
}
