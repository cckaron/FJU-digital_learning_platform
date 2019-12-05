<?php

namespace App\Repositories;

use App\User;

class UserRepository
{
    protected $user;

    /**
     * TeacherRepository constructor.
     * @param User $user
     */
    public function __construct(User $user){
        $this->user = $user;
    }

    public function find($id){
        return $this->user->find($id);
    }
}
