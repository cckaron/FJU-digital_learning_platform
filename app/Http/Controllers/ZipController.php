<?php

namespace App\Http\Controllers;


use App\Assignment;
use Chumper\Zipper\Facades\Zipper;

class ZipController extends Controller
{
    public function downloadZIP($student_id, $assignment_id) {
        // create a list of files that should be added to the archive.
        $files = glob(storage_path().'/app/public/'.$student_id.'/'.$assignment_id.'/*');
        $assignment = Assignment::where('id', $assignment_id)
            ->first();

        $common_course = $assignment->course()->common_course()->get();
        Zipper::make(storage_path().'/app/public/'.$student_id.'_'.'')->add($files)->close();
        return response()->download(storage_path().'/app/public/file.zip')->deleteFileAfterSend(true);
    }
}
