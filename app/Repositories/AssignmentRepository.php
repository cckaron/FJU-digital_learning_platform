<?php


namespace App\Repositories;


use App\Assignment;
use App\Services\eventService;

class AssignmentRepository
{
    protected $assignment;

    /**
     * AssignmentRepository constructor.
     * @param Assignment $assignment
     */
    public function __construct(Assignment $assignment){
        $this->assignment = $assignment;
    }

    public function find($id){
        return $this->assignment->find($id);
    }

    public function update($id, $arr){
        $this->assignment->where('id', $id)->update($arr);
    }
}
