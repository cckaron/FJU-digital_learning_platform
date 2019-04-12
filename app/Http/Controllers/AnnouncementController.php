<?php

namespace App\Http\Controllers;

use App\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    public function getCreateAnnouncement()
    {
        //必須確保一個老師不會在同個學年度，在同一個共同課程中開設2個以上的課程
        $teacher = Teacher::where('users_id', Auth::user()->id)->first();
        $courses = $teacher
            ->course()
            ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
            ->select('courses.*', 'common_courses.name as common_course_name', 'common_courses.status')
            ->where('status', '1')
            ->get();

        return view('announcement.createAnnouncement', [
            'courses' => $courses,
        ]);
    }

    public function postCreateAnnouncement(Request $request)
    {
        $request->validate([
            'announcementTitle' => 'required',
            'announcementContent' => 'required',
            'courses_id' => 'required'
        ]);

//        dd($request->all());

        $courses_id = $request->input('courses_id');

//        Log::info(print_r($courses_id,true));

        foreach ($courses_id as $course_id) {
            DB::table('course_announcement')
                ->insert([
                    'title' => $request->input('announcementTitle'),
                    'content' => $request->input('announcementContent'),
                    'courses_id' => $course_id,
                    'priority' => $request->input('topPost') ? 0 : 1, // if topPost == true, set 0, or set 1
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
        }

        return redirect()->back()->with('message', '發佈公告成功!');
    }

    public function getDeleteAnnouncement(){

    }

    public function getCreateSystemAnnouncement()
    {
        return view('announcement.createSystemAnnouncement');
    }

    public function postCreateSystemAnnouncement(Request $request)
    {
        $request->validate([
            'announcementTitle' => 'required',
            'announcementContent' => 'required',
        ]);

        $file = $request->file('file'); //default file name from request is "file"

        Log::info(print_r($file,true));

        DB::table('system_announcement')
            ->insert([
                'title' => $request->input('announcementTitle'),
                'content' => $request->input('announcementContent'),
                'priority' => $request->input('topPost') ? 0 : 1, // if topPost == true, set 0, or set 1
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);


        return redirect()->back()->with('message', '發佈公告成功!');
    }

    public function uploadAttachment(Request $request){
        // TODO Dropzone JS with Normal Form

        $student_id = Auth::user()->id;
        $student_assignment_id = $request->input('student_assignment_id');

        $assignment_id = DB::table('student_assignment')
            ->where('id', $student_assignment_id)
            ->value('assignments_id');

        $file = $request->file('file'); //default file name from request is "file"
        $filename = $file->getClientOriginalName();
        $filepath = $student_id.'/'.$assignment_id;

        $filename = str_replace(' ', '_', $filename);
        //this line is really important!!!!!!!!!!!!!!
        setlocale(LC_ALL,'en_US.UTF-8');

        Storage::disk('public')->putFileAs(
            $filepath, $file, $filename
        );

        //如果 remark 已經存在 (學生填寫過內容了)，就把狀態改成已繳交
        $old_remark = DB::table('student_assignment')
            ->where('id', $student_assignment_id)
            ->value('remark');
        if ($old_remark != null){
            DB::table('student_assignment')
                ->where('id', $student_assignment_id)
                ->update(['status' => 2,'updated_at' => Carbon::now()]);
        } else {
            DB::table('student_assignment')
                ->where('id', $student_assignment_id)
                ->update(['updated_at' => Carbon::now()]);
        }


    }

    public function deleteAttachment(Request $request){
        $student_id = Auth::user()->id;
        $student_assignment_id = $request->get('student_assignment_id');

        $assignment_id = DB::table('student_assignment')
            ->where('id', $student_assignment_id)
            ->value('assignments_id');

        $filename = $request->get('filename');

        $filepath = 'public/'.$student_id.'/'.$assignment_id.'/'.$filename;

        Storage::delete($filepath);

        $files = Storage::files('public/'.$student_id.'/'.$assignment_id);

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

    public function getAttachmentDetail(Request $request){
        $student_id = Auth::user()->id;
        $student_assignment_id = $request->get('student_assignment_id');

        $assignment_id = DB::table('student_assignment')
            ->where('id', $student_assignment_id)
            ->value('assignments_id');

        $path = 'public/'.$student_id.'/'.$assignment_id;

        $filepaths = Storage::allFiles($path);

        $filenames = array();

        $filesizes = array();

        //this line is really important!!!!!!!!!!!!!!
        setlocale(LC_ALL,'en_US.UTF-8');

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

    public function downloadAttachment($first, $second, $third, $fourth){
//        return Storage::download('public/505102236/1/midterm.py');

        $filepath = $first.'/'.$second.'/'.$third.'/'.$fourth;
//        return Storage::download($filepath);

        return response()->download(storage_path().'/app/'.$filepath);
    }
}
