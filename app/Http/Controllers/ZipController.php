<?php

namespace App\Http\Controllers;


use App\Assignment;
use Chumper\Zipper\Facades\Zipper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ZipController extends Controller
{
    public function downloadZIP($student_id, $assignment_id) {
        //this line is really important!!!!!!!!!!!!!!
        setlocale(LC_ALL,'en_US.UTF-8');
        
        // create a list of files that should be added to the archive.
        $files = glob(storage_path().'/app/public/'.$student_id.'/'.$assignment_id.'/*');
        if ($files == null){
            return redirect()->back()->with('message', '該作業無上傳檔案');
        }
        $assignment = Assignment::where('id', $assignment_id)
            ->first();
        $assignent_name = $assignment->name;

        $common_courses_id = $assignment
            ->course()
            ->first()->common_courses_id;

        $common_course_name = DB::table('common_courses')
            ->where('id', $common_courses_id)
            ->value('name');


//        Log::info(print_r($course, true));
        Zipper::make(storage_path().'/app/public/'.$student_id.'_'.$common_course_name.'_'.$assignent_name.'.zip')->add($files)->close();
        return response()->download(storage_path().'/app/public/'.$student_id.'_'.$common_course_name.'_'.$assignent_name.'.zip')->deleteFileAfterSend(true);
    }
}
