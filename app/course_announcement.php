<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class course_announcement extends Model
{
    protected $table = 'course_announcement';
    protected $fillable = ['id', 'courses_id', 'title', 'content', 'priority', 'status', 'created_at', 'updated_at'];

    public function course(){
        return $this->belongsTo('App\Course', 'courses_id', 'id');
    }
}
