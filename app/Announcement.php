<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    public $incrementing = false;
    protected $fillable = ['id', 'title', 'content', 'priority', 'status', 'created_at', 'updated_at'];

    public function course(){
        return $this
            ->belongsToMany('App\Course', 'course_announcement', 'announcements_id', 'courses_id', 'id', 'id');
    }
}
