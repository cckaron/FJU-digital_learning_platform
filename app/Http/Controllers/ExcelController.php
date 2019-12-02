<?php

namespace App\Http\Controllers;

use App\Imports\CommoncourseImport;
use App\Imports\CourseImport;
use App\Imports\GradeImport;
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

        //        Storage::disk('public')->delete($filePath);
    }

    public function importGrade(Request $request){
        $id = Auth::user()->id;

        $teacher_id = $request->input('teacher_id');
        $file = $request->file('file'); //default file name from request is "file"
        $filename = $file->getClientOriginalName();
        $filePath = $id.'/import';

        Storage::disk('public')->putFileAs(
            $filePath, $file, $filename
        );

        $FullFilePath = 'public/'.$filePath.'/'.$filename;
        Excel::import(new GradeImport($teacher_id), $FullFilePath);
    }
}
