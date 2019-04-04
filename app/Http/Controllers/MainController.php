<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MainController extends Controller
{
    public function getIndex(){
        $type = Auth::user()->type;

        $controller = new AssignmentController();
        $indexController = new IndexController();

        if ($type == 3){
            return $indexController->getTeacherIndex();
        } else if ($type == 4){
            return $indexController->getStudentIndex();
        }
        return view('dashboard.index');
    }
}
