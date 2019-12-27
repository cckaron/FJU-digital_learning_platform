<?php

namespace App\Services;

use App\Course;
use App\Repositories\CommonCourseRepository;
use App\Ta;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redirect;
use Exception;

class CommonCourseService implements eventService
{
    private $commonCourseRepository;


    /**
     * UserService constructor.
     * @param $commonCourseRepository $courseRepository
     */
    public function __construct(CommonCourseRepository $commonCourseRepository)
    {
        $this->commonCourseRepository = $commonCourseRepository;
    }

    public function find($id){
        try {
            return $this->commonCourseRepository->find($id);
        } catch (Exception $exception){
            return Redirect::back()->withErrors(['message', $exception->getMessage()]);
        }
    }

    public function get($status){
        try {
            return $this->commonCourseRepository->get($status);
        } catch (Exception $exception){
            return Redirect::back()->withErrors(['message', $exception->getMessage()]);
        }
    }

    public function all(){
        try {
            return $this->commonCourseRepository->all();
        } catch (Exception $exception){
            return Redirect::back()->withErrors(['message', $exception->getMessage()]);
        }
    }

    public function update($id, $arr){
        $this->commonCourseRepository->update($id, $arr);
    }

    public function dueOrNot($id)
    {
        $date = Carbon::parse($this->commonCourseRepository->find($id)->end_date);
        return Carbon::today()->gt($date) ? true : false; //if now time is greater than due date, then it is due.
    }
}
