<?php

namespace App\Repositories;

use App\Announcement;
use App\system_announcement;

class SystemAnnouncementsRepository
{
    protected $sys_announcement;

    /**
     * CourseRepository constructor.
     * @param system_announcement $sys_announcement
     */
    public function __construct(system_announcement $sys_announcement){
        $this->sys_announcement = $sys_announcement;
    }

    public function getPaginate($pag=5){
        return $announcements = system_announcement::orderBy('priority')
            ->orderBy('updated_at', 'desc')
            ->paginate($pag);
    }
}
