<?php

namespace App\Repositories;

use App\Announcement;

class AnnouncementRepository
{
    protected $announcement;

    /**
     * CourseRepository constructor.
     * @param Announcement $announcement
     */
    public function __construct(Announcement $announcement){
        $this->announcement = $announcement;
    }
}
