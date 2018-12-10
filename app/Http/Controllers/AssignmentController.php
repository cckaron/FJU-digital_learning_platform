<?php

namespace App\Http\Controllers;

use App\Assignment;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Hashids\Hashids;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class AssignmentController extends Controller
{
    public function getCreateAssignment(){
        $course_id = "";
        $course_name = "";

        $teacher_id = Auth::user()->id;


        if (DB::table('teachers')->where('users_id', $teacher_id)->exists()){
            $teacher = DB::table('teachers')->where('users_id', $teacher_id)->first();
            $course_id = $teacher->courses_id;
        }


        if (DB::table('courses')->where('id', $course_id)->exists()){
            $course = DB::table('courses')->where('id', $course_id)->first();
            $course_name = $course->name;
        }

        return view('Assignment.createAssignment', ['course_name' => $course_name]);
    }

    public function postCreateAssignment(Request $request){

        $teacher_id = Auth::user()->id;
        $course = DB::table('teachers')->where('users_id', $teacher_id)->first();
        $course_id = $course->courses_id;

        $assignment = new Assignment([
            'courses_id' => $course_id,
            'name' => $request->input('assignmentName'),
            'start_date' => $request->input('assignmentStart'),
            'end_date' => $request->input('assignmentEnd'),
        ]);

        $assignment->save();

        //get the assignment id which was just saved
        $assignment_id = $assignment->id;
        $assignment_name = $assignment->name;


        //create assignment for students
        //create student_course first

        //get the course's student count
        $students = DB::table('students')->where('courses_id', $course_id)->get();

        for ($i=0; $i<count($students); $i++){

            $student = $students[$i];
            $student_id = $student->users_id;

            DB::table('student_assignment')
                ->insert([
                    ['students_id' => $student_id, 'assignments_id' => $assignment_id, 'assignments_name' => $assignment_name]
                ]);
        }

        return redirect()->back()->with('message', '新增作業成功！');
    }


    public function getAllAssignments(){
        return view('assignment.showAllAssignments');
    }

    public function getAssignments(){
        $student_id = Auth::user()->id;

        //進行中
        $courses_processing = collect();
        $teachers_processing = collect();

        //已結束
        $courses_finished = collect();
        $teachers_finished = collect();


        // 取得該學生的所有作業
        $assignments = DB::table('student_assignment')
            ->where('students_id', $student_id)
            ->get();

        // 取得所有作業的 ID
        $assignments_id = $assignments->pluck('assignments_id');



        // 目的：取得該作業的課程名稱
        // 將作業分類成 1.進行中 2.已結束

        //進行中的作業
        $assignments_processing = DB::table('assignments')
            ->whereIn('id', $assignments_id)
            ->where('status', 1)
            ->get();

        //進行中的作業的狀態
        $assignments_processing_status = array();

        $assignments_processing_id = $assignments_processing->pluck('id');

        for ($r=0; $r<count($assignments_processing_id); $r++){
            $status = DB::table('student_assignment')
                ->where('assignments_id', $assignments_processing_id[$r])
                ->where('students_id', $student_id)
                ->value('status');
            array_push($assignments_processing_status, $status);
        }

        //進行中的作業的成績
        $assignments_processing_score = array();

        for ($r=0; $r<count($assignments_processing_id); $r++){
            $score = DB::table('student_assignment')
                ->where('assignments_id', $assignments_processing_id[$r])
                ->where('students_id', $student_id)
                ->value('score');
            array_push($assignments_processing_score, $score);
        }


        //取得assignment id 然後 hash
        $assignments_processing_id = $assignments_processing->pluck('id');
        for ($k=0; $k<count($assignments_processing_id); $k++){
            $hashids = new Hashids('assignment_id', 10);
            $assignments_processing_id[$k] = $hashids->encode($assignments_processing_id[$k]);
        }

        //取得 assignment course id 然後 hash
        $assignments_processing_course_id = $assignments_processing->pluck('courses_id');
        for ($k=0; $k<count($assignments_processing_course_id); $k++){
            $hashids = new Hashids('course_id', 6);
            $assignments_processing_course_id[$k] = $hashids->encode($assignments_processing_course_id[$k]);
        }

        // 基本資料
        $assignments_processing_name = $assignments_processing->pluck('name');
        $courses_processing_id = $assignments_processing->pluck('courses_id');
        $courses_processing_end_date = $assignments_processing->pluck('end_date');


        //已結束的作業
        $assignments_finished = DB::table('assignments')
            ->whereIn('id', $assignments_id)
            ->where('status', 0)
            ->get();

        //已結束的作業的狀態
        $assignments_finished_status = array();

        $assignments_finished_id = $assignments_finished->pluck('id');


        for ($r=0; $r<count($assignments_finished_id); $r++){
            $status = DB::table('student_assignment')
                ->where('assignments_id', $assignments_finished_id[$r])
                ->where('students_id', $student_id)
                ->value('status');
            array_push($assignments_finished_status, $status);
        }

        //已結束的作業的成績
        $assignments_finished_score = array();

        for ($r=0; $r<count($assignments_finished_id); $r++){
            $score = DB::table('student_assignment')
                ->where('assignments_id', $assignments_finished_id[$r])
                ->where('students_id', $student_id)
                ->value('score');
            array_push($assignments_finished_score, $score);
        }

        //取得 assignment id 然後 hash
        $assignments_finished_id = $assignments_finished->pluck('id');
        for ($k=0; $k<count($assignments_finished_id); $k++){
            $hashids = new Hashids('assignment_id', 10);
            $assignments_finished_id[$k] = $hashids->encode($assignments_finished_id[$k]);
        }

        //取得 assignment course id 然後 hash
        $assignments_finished_course_id = $assignments_finished->pluck('courses_id');
        for ($k=0; $k<count($assignments_finished_course_id); $k++){
            $hashids = new Hashids('course_id', 6);
            $assignments_finished_course_id[$k] = $hashids->encode($assignments_finished_course_id[$k]);
        }

        // 基本資料
        $assignments_finished_name = $assignments_finished->pluck('name');
        $courses_finished_id = $assignments_finished->pluck('courses_id');
        $courses_finished_end_date = $assignments_finished->pluck('end_date');


        //進行中的作業
        for ($i=0; $i<count($assignments_processing); $i++){

            // 取得進行中作業的指導老師，並且加入 $teachers_processing 集合中
            $teachers = DB::table('teacher_course')
                ->where('courses_id', $courses_processing_id[$i])
                ->get();
            $teacher_count = DB::table('teacher_course')
                ->where('courses_id', $courses_processing_id[$i])
                ->count();

            $teacher_array = array();
            for ($j=0; $j<$teacher_count; $j++){
                $teacher_name = DB::table('teachers')
                    ->where('users_id', $teachers[$j]->teachers_id)
                    ->value('users_name');

                array_push($teacher_array, $teacher_name);
            }

            $teachers_processing->push($teacher_array);

            $course = DB::table('courses')
                ->where('id', $assignments_processing[$i]->courses_id)
                ->get();

            $courses_processing->push($course);
        }

        //已結束的作業
        for ($i=0; $i<count($assignments_finished); $i++){

            // 取得進行中作業的指導老師，並且加入 $teachers_processing 集合中
            $teachers = DB::table('teacher_course')
                ->where('courses_id', $courses_finished_id[$i])
                ->get();
            $teacher_count = DB::table('teacher_course')
                ->where('courses_id', $courses_finished_id[$i])
                ->count();

            $teacher_array = array();
            for ($j=0; $j<$teacher_count; $j++){
                $teacher_name = DB::table('teachers')
                    ->where('users_id', $teachers[$j]->teachers_id)
                    ->value('users_name');

                array_push($teacher_array, $teacher_name);
            }

            $teachers_finished->push($teacher_array);

            $course = DB::table('courses')
                ->where('id', $assignments_finished[$i]->courses_id)
                ->get();

            $courses_finished->push($course);
        }

        return view('assignment.showAssignments', [
            'assignments' => $assignments,

            'assignments_processing' => $assignments_processing,
            'assignments_processing_id' => $assignments_processing_id,
            'assignments_processing_course_id' => $assignments_processing_course_id,
            'assignments_processing_name' => $assignments_processing_name,
            'assignments_processing_status' => $assignments_processing_status,
            'assignments_processing_score' => $assignments_processing_score,
            'courses_processing'=>$courses_processing,
            'courses_processing_end_date' => $courses_processing_end_date,
            'teachers_processing' => $teachers_processing,

            'assignments_finished' => $assignments_finished,
            'assignments_finished_id' => $assignments_finished_id,
            'assignments_finished_course_id' => $assignments_finished_course_id,
            'assignments_finished_name' => $assignments_finished_name,
            'assignments_finished_status' => $assignments_finished_status,
            'assignments_finished_score' => $assignments_finished_score,
            'courses_finished' => $courses_finished,
            'courses_finished_end_date' => $courses_finished_end_date,
            'teachers_finished' => $teachers_finished,

         ]);
    }

    public function getAssignments_Teacher(){
        $teacher_id = Auth::user()->id;

        //找出這個老師的課程
        $courses = DB::table('teacher_course')
            ->where('teachers_id', $teacher_id)
            ->get();

        $courses_id = $courses->pluck('courses_id');

        //找出這個老師的課程的作業
        //進行中的作業
        $assignments_processing = DB::table('assignments')
            ->whereIn('courses_id', $courses_id)
            ->where('status', 1)
            ->get();

        //該作業的課程ID
        $courses_processing_id = $assignments_processing->pluck('courses_id');

        //該作業的名稱
        $assignments_processing_name = $assignments_processing->pluck('name');

        //取得該作業的課程資訊
        $courses_processing_year = array();
        $courses_processing_semester = array();
        $courses_processing_end_date = array();

        //取得該作業的指導老師
        $teachers_processing = array();

        for ($i=0; $i<count($assignments_processing); $i++) {

            //課程資訊
            $course = DB::table('courses')
                ->where('id', $courses_processing_id[$i])
                ->first();

            $year = $course->year;
            $semester = $course->semester;
            $end_date = $course->end_date;

            array_push($courses_processing_year, $year);
            array_push($courses_processing_semester, $semester);
            array_push($courses_processing_end_date, $end_date);

            //指導老師
            $teachers = DB::table('teacher_course')
                ->where('courses_id', $courses_processing_id[$i])
                ->get();

            //指導老師的人數
            $teacher_count = DB::table('teacher_course')
                ->where('courses_id', $courses_processing_id[$i])
                ->count();

            $teacher_array = array();
            for ($j = 0; $j < $teacher_count; $j++) {
                $teacher_name = DB::table('teachers')
                    ->where('users_id', $teachers[$j]->teachers_id)
                    ->value('users_name');

                array_push($teacher_array, $teacher_name);
            }

            array_push($teachers_processing, $teacher_array);

        }


        //已結束的作業
        $assignments_finished = DB::table('assignments')
            ->whereIn('courses_id', $courses_id)
            ->where('status', 0)
            ->get();

        //該作業的課程ID
        $courses_finished_id = $assignments_finished->pluck('courses_id');

        //該作業的名稱
        $assignments_finished_name = $assignments_finished->pluck('name');


        //取得該作業的課程資訊
        $courses_finished_year = array();
        $courses_finished_semester = array();
        $courses_finished_end_date = array();


        //取得該作業的指導老師
        $teachers_finished = array();

        for ($i=0; $i<count($assignments_finished); $i++) {

            //課程資訊
            $course = DB::table('courses')
                ->where('id', $courses_finished_id[$i])
                ->first();

            $name = $course->name;
            $year = $course->year;
            $semester = $course->semester;
            $end_date = $course->end_date;

            array_push($courses_finished_year, $year);
            array_push($courses_finished_semester, $semester);
            array_push($courses_finished_end_date, $end_date);

            //指導老師
            $teachers = DB::table('teacher_course')
                ->where('courses_id', $courses_finished_id[$i])
                ->get();

            //指導老師的人數
            $teacher_count = DB::table('teacher_course')
                ->where('courses_id', $courses_finished_id[$i])
                ->count();

            $teacher_array = array();
            for ($j = 0; $j < $teacher_count; $j++) {
                $teacher_name = DB::table('teachers')
                    ->where('users_id', $teachers[$j]->teachers_id)
                    ->value('users_name');

                array_push($teacher_array, $teacher_name);
            }

            array_push($teachers_finished, $teacher_array);

        }

        return view('assignment.showAssignments_Teacher', [
            'assignments_processing' => $assignments_processing,
           'assignments_processing_name' => $assignments_processing_name,
           'courses_processing_id' => $courses_processing_id,
           'courses_processing_year' => $courses_processing_year,
           'courses_processing_semester' => $courses_processing_semester,
            'courses_processing_end_date' => $courses_processing_end_date,
            'teachers_processing' => $teachers_processing,

            'assignments_finished' => $assignments_finished,
            'assignments_finished_name' => $assignments_finished_name,
            'courses_finished_id' => $courses_finished_id,
            'courses_finished_year' => $courses_finished_year,
            'courses_finished_semester' => $courses_finished_semester,
            'courses_finished_end_date' => $courses_finished_end_date,
            'teachers_finished' => $teachers_finished,


        ]);
    }

    public function getAllAssignments_dt(){
        return DataTables::of(Assignment::query())
            ->editColumn('updated_at', function(Assignment $assignment){
                return $assignment->updated_at->diffForHumans();
            })
            ->make(true);
    }

    public function getHandInAssignment($course_id, $assignment_id){
        $student_id = Auth::user()->id;

        $encode_course_id = new Hashids('course_id', 6);
        $encode_assignment_id = new Hashids('assignment_id', 10);
        $course_id = $encode_course_id->decode($course_id);
        $assignment_id = $encode_assignment_id->decode($assignment_id);

        $student_assignment = DB::table('student_assignment')
            ->where('students_id', $student_id)
            ->where('assignments_id', $assignment_id)
            ->first();

        $student_assignment_id = $student_assignment->id;

        $student_assignment_status = $student_assignment->status;

        $remark = $student_assignment->remark;

        $comment = $student_assignment->comment;

        return view('assignment.handInAssignment', [
            'course_id' => $course_id,
            'assignment_id' => $assignment_id,
            'student_assignment_id' => $student_assignment_id,
            'remark' => $remark,
            'comment' => $comment,
            'student_assignment_status' => $student_assignment_status,
            ]);
    }

    public function postHandInAssignment(Request $request, $course_id, $assignment_id){
        $remark = $request->input('remark');
        $student_assignment_id = $request->input('student_assignment_id');
        DB::table('student_assignment')
            ->where('id', $student_assignment_id)
            ->update(['remark' => $remark]);

        return redirect()->back()->with('message', '繳交作業成功！');
    }

    public function uploadAssignment(Request $request){
        $student_id = Auth::user()->id;
        $student_assignment_id = $request->input('student_assignment_id');

        $file = $request->file('file'); //default file name from request is "file"
        $filename = $file->getClientOriginalName();
        $filepath = $student_id.'/'.$student_assignment_id;

        $file->storeAs($filepath, $filename);

//        $file = new Filesystem();
//        $directory = 'app/public/123';
//        if ( $file->isDirectory(storage_path($directory)) )
//        {
//        }
//        else
//        {
//            $file->makeDirectory(storage_path($directory), 777, true, true);
//        }

        Storage::disk('public')->putFileAs(
            $filepath, $file, $filename
        );

        DB::table('student_assignment')
            ->where('id', $student_assignment_id)
            ->update(['status' => 2]);


    }

    public function deleteAssignment(Request $request){
        $student_id = Auth::user()->id;
        $student_assignment_id = $request->get('student_assignment_id');
        $filename = $request->get('filename');

        $filepath = 'public/'.$student_id.'/'.$student_assignment_id.'/'.$filename;

        Storage::delete($filepath);

        $files = Storage::files('public/'.$student_id.'/'.$student_assignment_id);

        //如果資料夾內沒有檔案，將作業狀態更改為 1 => 未繳交
        if (empty($files)){
            DB::table('student_assignment')
                ->where('id', $student_assignment_id)
                ->update(['status' => 1]);
        }


        $output = array(
            'filepath' => $filepath,
            'student_assignment_id' => $student_assignment_id,
        );

        echo json_encode($output);
    }

    public function getAssignmentFileDetail(Request $request){
        $student_id = Auth::user()->id;
        $student_assignment_id = $request->get('student_assignment_id');

        $path = 'public/'.$student_id.'/'.$student_assignment_id;

        $filepaths = Storage::allFiles($path);

        $filenames = array();

        $filesizes = array();

        for($i=0; $i<count($filepaths); $i++){
            $filenames[$i] = basename($filepaths[$i]);
            $filesizes[$i] = Storage::size($filepaths[$i]);
        }

        $output = array(
            'filepaths' => $filepaths,
            'filenames' => $filenames,
            'filesizes' => $filesizes,
        );

        echo json_encode($output);


    }

    public function downloadAssignment($first, $second, $third, $fourth){
//        return Storage::download('public/505102236/1/midterm.py');

        $filepath = $first.'/'.$second.'/'.$third.'/'.$fourth;
//        return Storage::download($filepath);

        return response()->download(storage_path().'/app/'.$filepath);


    }
}
