<?php

namespace App\Services;

use App\Announcement;
use App\Course;
use App\Repositories\AnnouncementsRepository;
use App\Repositories\CourseRepository;
use App\Repositories\SystemAnnouncementsRepository;
use App\Repositories\TaRepository;
use App\Repositories\TeacherRepository;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Exception;

class SystemAnnouncementService
{
    private $sys_announcementRepository;

    public function __construct(SystemAnnouncementsRepository $sys_announcementRepository)
    {
        $this->sys_announcementRepository = $sys_announcementRepository;
    }

    public function getPaginateWithFileDetail()
    {
        $sys_announcements = $this->sys_announcementRepository->getPaginate();

        //put file info in collect()
        foreach($sys_announcements as $sys_announcement) {

            $fileNames = array();

            $folder_path = storage_path() . '/app/public/sys_announcement/' . $sys_announcement->id;

            if (!File::exists($folder_path)) {
                File::makeDirectory($folder_path, $mode = 0777, true, true);
            }

            setlocale(LC_ALL, 'en_US.UTF-8');

            try{
                $filesInFolder = File::files($folder_path);

                foreach ($filesInFolder as $path) {
                    $file = pathinfo($path);

                    if ($file['filename'] != 'blob') { //空的檔案
                        array_push($fileNames, $file['filename'] . '.' . $file['extension']);
                    }
                }
                $sys_announcement->fileNames = $fileNames;

            } catch (Exception $exception){
                return Redirect::back()->withErrors(['message', $exception->getMessage()]);
            }

            return $sys_announcements;
        }


    }
}
