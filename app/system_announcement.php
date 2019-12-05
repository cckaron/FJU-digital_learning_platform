<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class system_announcement extends Model
{
    protected $table = 'system_announcement';
    public $incrementing = false;
    protected $fillable = ['id', 'title', 'content', 'priority', 'status', 'created_at', 'updated_at'];
}
