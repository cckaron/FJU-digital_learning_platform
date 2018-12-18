<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AssignmentController;

class MainController extends Controller
{
    public function getIndex(){
        $type = Auth::user()->type;

        $controller = new AssignmentController();

        if ($type == 3){
            return $controller->getAssignments_Teacher();
        } else if ($type == 4){
            return $controller->getAssignments();
    }
        return view('dashboard.index');
    }
}
