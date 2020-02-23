<?php


namespace App\Services;


use App\Repositories\AssignmentRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Redirect;

class AssignmentService implements eventService
{
    protected $assignmentRepository;

    public function __construct(AssignmentRepository $assignmentRepository)
    {
        $this->assignmentRepository = $assignmentRepository;
    }

    public function find($id)
    {
        try {
            return $this->assignmentRepository->find($id);
        } catch (Exception $exception){
            return Redirect::back()->withErrors(['message', $exception->getMessage()]);
        }
    }

    public function update($id, $arr)
    {
        $this->assignmentRepository->update($id, $arr);
    }

    public function dueOrNot($id)
    {
        $assignment = $this->assignmentRepository->find($id);
        $timestamp = strtotime("$assignment->end_date $assignment->end_time");
        $dueTime = Carbon::createFromTimestamp($timestamp);
        return Carbon::now()->gt($dueTime) ? true : false; //if now time is greater than due date, then it is due.
    }
}
