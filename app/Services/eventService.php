<?php

namespace App\Services;

interface eventService
{
    public function find($id);
    public function dueOrNot($id);
}
