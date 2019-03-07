<?php

namespace App\Http\Controllers;

use App\Imports\CommoncourseImport;
use App\Imports\CourseImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    public function importCourse(Request $request){
        $id = Auth::user()->id;

        $file = $request->file('file'); //default file name from request is "file"
        $filename = $file->getClientOriginalName();
        $filePath = $id.'/import';

        Storage::disk('public')->putFileAs(
            $filePath, $file, $filename
        );

        $FullFilePath = 'public/'.$filePath.'/'.$filename;
        Excel::import(new CommoncourseImport(), $FullFilePath);
        //TODO teacher_course and student_course
//        Storage::disk('public')->delete($filePath);
    }
}
