<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Redirect;
use Exception;

class UserService
{
    private $userRepository;

    /**
     * UserService constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function findUser($user_id){
        try {
            //取得用戶資訊
            $user = $this->userRepository->find($user_id);
            return $user;
        } catch (Exception $exception){
            return Redirect::back()->withErrors(['message', $exception->getMessage()]);
        }
    }
}
