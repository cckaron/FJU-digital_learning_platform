<?php

namespace App\Repositories;

use App\Announcement;

class AnnouncementsRepository
{
    protected $announcement;

    /**
     * CourseRepository constructor.
     * @param Announcement $announcement
     */
    public function __construct(Announcement $announcement){
        $this->announcement = $announcement;
    }

    public function getPaginate($announcements_id, $pag=5){
        return $announcements = Announcement::whereIn('id', $announcements_id)
            ->orderBy('priority')
            ->orderBy('updated_at', 'desc')
            ->paginate($pag);
    }

    public function getCourseField($announcement_id, $field){
        $announcement = $this->announcement->where('id', $announcement_id)->first();
        $courseField = $announcement->course()->pluck('courses.'.$field)->toArray();
        return $courseField;
    }
}
