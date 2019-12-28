<?php

namespace App\Services;

interface eventService
{
    public function find($id);
    public function update($id, $arr);
    public function dueOrNot($id);
}
