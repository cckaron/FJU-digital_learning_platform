<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class course_announcement extends Model
{
    protected $table = 'course_announcement';
    protected $fillable = ['courses_id', 'announcements_id'];
}
