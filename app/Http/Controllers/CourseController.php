<?php

namespace App\Http\Controllers;

use App\common_course;
use App\Course;
use App\Student;
use App\Teacher;
use App\User;
use Carbon\Carbon;
use Hashids\Hashids;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class CourseController extends Controller
{

    //新增課程 (get)
    public function getAddCourse(){
        $teachers = DB::table('teachers')->get();

        $common_courses = DB::table('common_courses')->orderBy('name', 'asc')->get();

        $common_courses_name = $common_courses->pluck('name');

        $common_courses_id = $common_courses->pluck('id');

        return view('course.addCourse', [
            'teachers' => $teachers,
            'common_courses_name' => $common_courses_name,
            'common_courses_id' => $common_courses_id,
            ]);
    }

    //新增課程 (post)
    public function postAddCourse(Request $request){
        $request->validate([
            'courseTeachers' => 'required',
            'courseStudents' => 'required',
            'common_courses_name' => 'required',
            'courseName' => 'required'
        ]);

        $teachers = $request->input('courseTeachers');
        $students = $request->input('courseStudents');

        $common_courses_name =  $request->input('common_courses_name');

        $common_courses_id = DB::table('common_courses')
            ->where('name', $common_courses_name)
            ->value('id');

        //after save, it should be the last id inserted
        $course_id = DB::table('courses')
            ->insertGetId([
                'common_courses_id' => $common_courses_id,
                    'name' => $request->input('courseName'),
                    'class' => $request->input('courseClass'),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()]
            );

        //save courses_id to teacher table
        for ($i=0; $i<count($teachers); $i++){

            //add to teacher_course
            $teacher_id = DB::table('teachers')->where('users_name', '=', $teachers[$i])->value('users_id');
            DB::table('teacher_course')
                ->insert(
                    ['teachers_id' => $teacher_id, 'courses_id' => $course_id, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
                );
        }

        //save courses_id to student table
        for ($k=0; $k<count($students); $k++){

            //add to student_course
            DB::table('student_course')
                ->insert([
                    ['students_id' => (int)$students[$k],'courses_id' => $course_id, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
                ]);
        }
        return redirect()->back()->with('message', '已成功新增課程');
    }

    public function showCourseStudents($courses_id){
        // 先 decode course_id，用於DB查詢
        $encode_courses_id = new Hashids('courses_id', 7);
        $courses_id = $encode_courses_id->decode($courses_id)[0]; //decode 之後會變成 array
        $test = $courses_id;

        $student_ids = DB::table('student_course')
            ->where('courses_id', $courses_id)
            ->pluck('students_id');

        $students = DB::table('students')
            ->whereIn('users_id', $student_ids)
            ->get();


        //公告
        $course = Course::where('id', $courses_id)->first();
        $announcements = $course->announcement()->orderBy('priority')->orderBy('updated_at', 'desc')->paginate(5);
        //use ->paginate(), so don't need ->get()



        // encode 回來，這一頁還會用到 course_id
        $courses_id = $encode_courses_id->encode($courses_id);

        return view('course.showCourseStudent', [
            'students' => $students,
            'courses_id' => $courses_id,
            'test' => $test,
            'announcements' => $announcements,
            ]);

    }

    public function dropCourse($course_id, $student_id){
        // decode course_id，用於DB查詢
        $encode_courses_id = new Hashids('courses_id', 7);
        $course_id = $encode_courses_id->decode($course_id)[0]; //decode 之後會變成 array

        $student_name = DB::table('students')
            ->where('users_id', $student_id)
            ->value('users_name');

        DB::table('student_course')
            ->where('students_id', $student_id)
            ->where('courses_id', $course_id)
            ->delete();

        $course = Course::where('id', $course_id)->first();
        $assignments = $course->assignment()->get();

        foreach($assignments as $assignment){
            DB::table('student_assignment')
                ->where('students_id', $student_id)
                ->where('assignments_id', $assignment->id)
                ->where('courses_id', $course_id)
                ->delete();
        }

        return redirect()->back()->with('message', $student_name.' 退選成功!');
    }

    public function deleteCourse($course_id){
        // decode course_id，用於DB查詢
        $encode_courses_id = new Hashids('courses_id', 7);
        $course_id = $encode_courses_id->decode($course_id)[0]; //decode 之後會變成 array

        DB::table('courses')
            ->where('id', $course_id)
            ->delete();

        return redirect()->back()->with('message', '已成功刪除課程');
    }

    public function postChangeCourseContent(Request $request){
        $validation = Validator::make($request->all(), [
            'name' => 'required',

        ]);

        $name = $request->get('name');
        $course_id = $request->get('course_id');

        $error_array = array();
        $success_output = '';
        if ($validation->fails()){
            foreach($validation->messages()->getMessages() as $field_name => $messages)
            {
                $error_array[] = $messages;
            }
        } else {
            DB::table('courses')
                ->where('id', $course_id)
                ->update([
                    'name' => $name,
                ]);
            $success_output = '<div class="alert alert-success"> 修改成功！ </div>';
        }
        $output = array(
            'error' => $error_array,
            'success' => $success_output,
        );
        echo json_encode($output);
    }

    //所有課程列表 (get)
    public function getAllCourses(){
        $courses = Course::with('common_course')
            ->join('common_courses', 'courses.common_courses_id', 'common_courses.id')
            ->join('teacher_course', 'courses.id', 'teacher_course.courses_id')
            ->select('courses.*', 'courses.id as course_id', 'courses.name as course_name','common_courses.*', 'teacher_course.teachers_id')
            ->orderBy('common_courses.year')
            ->get();

        //hashid
        $hashids = new Hashids('courses_id', 7);
        foreach($courses as $course){
            $course->real_id = $course->course_id;
            $course->course_id = $hashids->encode($course->course_id);
        }

        return view('course.showAllCourses', [
            'courses' => $courses,

        ]);
    }

    public function getShowCourses_Teacher(){
        $teacher_id = Auth::user()->id;

        $teacher = Teacher::where('users_id', $teacher_id)->first();
        $teacher_courses_processing = $teacher->course()
            ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
            ->select('courses.*',
                'common_courses.id as common_course_id',
                'common_courses.name as common_course_name',
                'common_courses.status',
                'common_courses.year',
                'common_courses.semester',
                'common_courses.start_date',
                'common_courses.end_date'
            )
            ->where('status', '1')
            ->get();

        $teacher_courses_ended = $teacher->course()
            ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
            ->select('courses.*',
                'common_courses.id as common_course_id',
                'common_courses.name as common_course_name',
                'common_courses.status',
                'common_courses.year',
                'common_courses.semester',
                'common_courses.start_date',
                'common_courses.end_date'
            )
            ->where('status', '0')
            ->get();

        //hashids
        foreach($teacher_courses_processing as $teacher_course) {
            $hashids_common_course = new Hashids('common_courses_id', 5);
            $teacher_course->common_course_id = $hashids_common_course->encode($teacher_course->common_course_id);

            $hashids_course = new Hashids('courses_id', 7);
            $teacher_course->id = $hashids_course->encode($teacher_course->id);
        }

        foreach($teacher_courses_ended as $teacher_course) {
            $hashids = new Hashids('common_courses_id', 5);
            $teacher_course->common_course_id = $hashids->encode($teacher_course->common_course_id);

            $hashids_course = new Hashids('courses_id', 7);
            $teacher_course->id = $hashids_course->encode($teacher_course->id);
        }

        return view('course.showCourses_Teacher', [
            'teacher_courses_processing' => $teacher_courses_processing,
            'teacher_courses_ended' => $teacher_courses_ended
        ]);
    }

    public function getShowCourses_Student(){
        $student_id = Auth::user()->id;

        $student = Student::where('users_id', $student_id)->first();
        $student_courses_processing = $student->course()
            ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
            ->select('courses.*',
                'common_courses.id as common_course_id',
                'common_courses.name as common_course_name',
                'common_courses.status',
                'common_courses.year',
                'common_courses.semester',
                'common_courses.start_date',
                'common_courses.end_date'
            )
            ->where('status', '1')
            ->get();

        $student_courses_ended = $student->course()
            ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
            ->select('courses.*',
                'common_courses.id as common_course_id',
                'common_courses.name as common_course_name',
                'common_courses.status',
                'common_courses.year',
                'common_courses.semester',
                'common_courses.start_date',
                'common_courses.end_date'
            )
            ->where('status', '0')
            ->get();

        //hashids
        foreach($student_courses_processing as $student_course) {
            $hashids_common_course = new Hashids('common_courses_id', 5);
            $student_course->common_course_id = $hashids_common_course->encode($student_course->common_course_id);

            $hashids_course = new Hashids('courses_id', 7);
            $student_course->id = $hashids_course->encode($student_course->id);
        }

        foreach($student_courses_ended as $student_course) {
            $hashids = new Hashids('common_courses_id', 5);
            $student_course->common_course_id = $hashids->encode($student_course->common_course_id);

            $hashids_course = new Hashids('courses_id', 7);
            $student_course->id = $hashids_course->encode($student_course->id);
        }

        return view('course.showCourses_Student', [
            'student_courses_processing' => $student_courses_processing,
            'student_courses_ended' => $student_courses_ended
        ]);
    }

    public function getShowSingleCourse($common_courses_id){
        $id = Auth::user()->id;
        $role = Auth::user()->type;

        //把 $common_course_id 解碼
        $encode_common_course_id = new Hashids('common_courses_id', 5);
        $common_courses_id = $encode_common_course_id->decode($common_courses_id);
        $common_courses_id = $common_courses_id[0]; //because decode() return array

        //找出所有課程中，該課程的 共同課程id 等於 $common_course_id 的課程
        $courses = DB::table('courses')
            ->where('common_courses_id', $common_courses_id)
            ->get();

        $all_courses_id = $courses->pluck('id');

        if ($role == 3){ //如果是老師的話
            //篩選出這個共同課程中，是此老師開的課程
            $courses = DB::table('teacher_course')
                ->whereIn('courses_id', $all_courses_id)
                ->where('teachers_id', $id)
                ->get();
        } else if ($role == 4){ //如果是學生
            //篩選出這個共同課程中，有這個學生的課程
            $courses = DB::table('student_course')
                ->whereIn('courses_id', $all_courses_id)
                ->where('students_id', $id)
                ->get();
        }

        //取得是此老師開的課程的 課程id
        $courses_id = $courses->pluck('courses_id');

        //再來取得這個課程的指導老師
        $courses_teachers = array();

        for($i=0; $i<count($courses_id); $i++){
            $teacher_course = DB::table('teacher_course')
                ->where('courses_id', $courses_id[$i])
                ->get();

            $teachers_id = $teacher_course->pluck('teachers_id');

            $teachers_name = array();

            for($k=0; $k<count($teachers_id); $k++){
                $teacher_name = DB::table('users')
                    ->where('id', $teachers_id[$k])
                    ->value('name');

                array_push($teachers_name, $teacher_name);
            }

            array_push($courses_teachers, $teachers_name);
        }

        //課程基本資料
        $courses_name = array();

        for ($k=0; $k<count($courses_id); $k++){
            $course_name = DB::table('courses')
                ->where('id', $courses_id[$k])
                ->value('name');
            array_push($courses_name, $course_name);
        }

        $common_course = DB::table('common_courses')
            ->where('id', $common_courses_id)
            ->first();

        //hash common course id
        $hashids = new Hashids('common_courses_id', 5);
        $common_courses_id = $hashids->encode($common_courses_id);


        //hash course id
        for($i=0; $i<count($courses_id); $i++){

            $hashids = new Hashids('courses_id', 7);
            $hashed_course_id = $hashids->encode($courses_id[$i]);

            $courses_id[$i] = $hashed_course_id;
        }


        return view('course.showSingleCourse', [
            'common_course' => $common_course,
            'common_courses_id' => $common_courses_id,
            'courses_id' => $courses_id,
            'courses_teachers' => $courses_teachers,
            'courses_name' => $courses_name,
        ]);
    }

    //Datatables
    public function getUsers_dt(){
        return DataTables::of(Student::query()->orderBy('grade', 'asc')->orderBy('class', 'desc'))
            ->editColumn('created_at', function(Student $student){
                return $student->created_at->diffForHumans();
            })
            ->addColumn('checkbox', function (Student $student) {
//                return '<div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="'.$user->id.'" name="'.$user->id.'" /><label class="custom-control-label" for="'.$user->id.'"></label></div>';
                return '<label class="customcheckbox"><input type="checkbox" class="listCheckbox" name="courseStudents[]" value="'.$student->users_id.'"/><span class="checkmark"></span></label>';
            })
            ->rawColumns(['checkbox'])
            ->make(true);
    }

    public function getAllCourses_dt(){
        return DataTables::of(Course::query())
            ->editColumn('updated_at', function(Course $course){
                return $course->updated_at->diffForHumans();
            })
            ->make(true);
    }

    public function signClass_ajax(Request $request){
        $validation = Validator::make($request->all(), [
            'student_number' => [
                'required',
                'exists:users,id',
                function($attribute, $value, $fail) {
                    if (!mb_detect_encoding($value, 'ASCII', true)) {
                        return $fail('帳號 不可含有非 英文/數字 的字元');
                    }
                },
            ]
        ]);

        $error_array = array();
        $success_output = '';

        $courses_id = $request->input('courses_id');
        $encode_courses_id = new Hashids('courses_id', 7);
        $courses_id = $encode_courses_id->decode($courses_id)[0]; //decode 之後會變成 array
        $student_number = $request->input('student_number');

        if ($validation->fails()){
            foreach($validation->messages()->getMessages() as $field_name => $messages)
            {
                $error_array[] = $messages;
            }
        } else {
            if ($request->get('button_action') == "插入")
            {
                if (!DB::table('student_course')
                    ->where('courses_id', $courses_id)
                    ->where('students_id', $student_number)
                    ->exists())
                {
                    DB::table('student_course')
                        ->insert([
                            'courses_id' => $courses_id,
                            'students_id' => $student_number,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]);

                    $course = Course::where('id', $courses_id)->first();
                    $assignments = $course->assignment()->get();

                    foreach($assignments as $assignment){
                        DB::table('student_assignment')
                            ->insert([
                                'students_id' => $student_number,
                                'assignments_id' => $assignment->id,
                                'status' => 1,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ]);
                    }

                    $success_output = '<div class="alert alert-success"> 加選成功！ </div>';
                } else {
                    $success_output = '<div class="alert alert-success"> 加選錯誤！該學生已於課程內。 </div>';
                }
            }

        }
        $output = array(
            'error' => $error_array,
            'success' => $success_output
        );
        echo json_encode($output);
    }

}
