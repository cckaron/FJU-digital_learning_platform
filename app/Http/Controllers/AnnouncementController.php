<?php

namespace App\Http\Controllers;

use App\Announcement;
use App\Course;
use App\Teacher;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    //系統公告
    //新增
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

        $files = $request->file('file'); //default file name from request is "file"

        $sys_announcementID = DB::table('system_announcement')
            ->insertGetId([
                'title' => $request->input('announcementTitle'),
                'content' => $request->input('announcementContent'),
                'priority' => $request->input('topPost') ? 0 : 1, // if topPost == true, set 0, or set 1
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

        if ($files){
            foreach ($files as $file){
                $filename = $file->getClientOriginalName();
                $filepath = 'sys_announcement/'.$sys_announcementID;

                $filename = str_replace(' ', '_', $filename);
                //this line is really important!!!!!!!!!!!!!!
                setlocale(LC_ALL,'en_US.UTF-8');

                Storage::disk('public')->putFileAs(
                    $filepath, $file, $filename
                );
            }
        }

        return redirect()->back()->with('message', '發佈公告成功!');
    }

    //編輯
    public function getEditSystemAnnouncement($id){
        $system_announcement = DB::table('system_announcement')
            ->where('id', $id)
            ->first();
        //get file detail

        $path = 'public/sys_announcement/'.$id;
        $filepaths = Storage::allFiles($path);

        $filenames = array();

        $filesizes = array();

        //this line is really important!!!!!!!!!!!!!!
        setlocale(LC_ALL,'en_US.UTF-8');

        for($i=0; $i<count($filepaths); $i++){
            $filenames[$i] = basename($filepaths[$i]);
            $filesizes[$i] = Storage::size($filepaths[$i]);
        }

        $files = array(
            'filepaths' => $filepaths,
            'filenames' => $filenames,
            'filesizes' => $filesizes,
        );

        return view('announcement.editSystemAnnouncement', [
            'files' => $files,
            'system_announcement' => $system_announcement
        ]);
    }
    public function postEditSystemAnnouncement(Request $request)
    {
        $request->validate([
        ]);


        $id = $request->get('announcement_id');
        $files = $request->file('file'); //default file name from request is "file"

        try{
            DB::table('system_announcement')
                ->where('id', $id)
                ->update([
                    'title' => $request->input('announcementTitle'),
                    'content' => $request->input('announcementContent'),
                    'priority' => $request->input('topPost') ? 0 : 1, // if topPost == true, set 0, or set 1
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
        } catch (QueryException $e){
            Log::info($e);
        }


        if ($files){
            foreach ($files as $file){
                $filename = $file->getClientOriginalName();
                $filepath = 'sys_announcement/'.$id;

                $filename = str_replace(' ', '_', $filename);
                //this line is really important!!!!!!!!!!!!!!
                setlocale(LC_ALL,'en_US.UTF-8');

                Storage::disk('public')->putFileAs(
                    $filepath, $file, $filename
                );
            }
        }

        return redirect()->back()->with('message', '修改公告成功!');
    }

    //刪除
    public function getDeleteSystemAnnouncement($id){
        DB::table('system_announcement')
            ->where('id', $id)
            ->delete();

        $filepath = 'public/sys_announcement/'.$id;
        Storage::deleteDirectory($filepath);

        return redirect()->back()->with('message', '刪除成功!');
    }

    //顯示
    public function getShowSystemAnnouncement(){
        //系統公告
        $sys_announcements = DB::table('system_announcement')
            ->orderBy('priority')
            ->orderBy('updated_at', 'desc')
            ->paginate(5);

        return view('announcement.showSystemAnnouncement', [
            'sys_announcements' => $sys_announcements,
        ]);
    }


    //課程公告
    //新增
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

        $announcement_id = DB::table('announcements')
            ->insertGetId([
                'title' => $request->input('announcementTitle'),
                'content' => $request->input('announcementContent'),
                'priority' => $request->input('topPost') ? 0 : 1, // if topPost == true, set 0, or set 1
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

        foreach ($courses_id as $course_id) {
            DB::table('course_announcement')
                ->insert([
                    'courses_id' => $course_id,
                    'announcements_id' => $announcement_id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
        }

        return redirect()->back()->with('message', '發佈公告成功!');
    }

    //編輯
    public function getEditAnnouncement($id){
        $announcement = DB::table('announcements')
            ->where('id', $id)
            ->first();

        return view('announcement.editAnnouncement', [
            'announcement' => $announcement
        ]);
    }

    public function postEditAnnouncement(Request $request)
    {
        $id = $request->get('announcement_id');
        try{
            DB::table('announcements')
                ->where('id', $id)
                ->update([
                    'title' => $request->input('announcementTitle'),
                    'content' => $request->input('announcementContent'),
                    'priority' => $request->input('topPost') ? 0 : 1, // if topPost == true, set 0, or set 1
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
        } catch (QueryException $e){
            Log::info($e);
        }

        return redirect()->back()->with('message', '修改公告成功!');
    }

    //刪除
    public function getDeleteAnnouncement($id){
        DB::table('announcements')
            ->where('id', $id)
            ->delete();

        return redirect()->back()->with('message', '刪除成功!');
    }

    //顯示
    public function getShowAnnouncement(){
        $teacher_id = Auth::user()->id;
        $teacher = Teacher::where('users_id', $teacher_id)->first();

        //取得所有課程
        $courses = $teacher->course()
            ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
            ->select('courses.id', 'courses.name', 'courses.common_courses_id', 'common_courses.status as status', 'common_courses.name as com_name')
            ->where('status', 1)
            ->get(); //in progress

        //取得所有課程中的公告id, 篩選出不重複的id
        $announcements_id = array();
        foreach($courses as $course){
            $announcement_id = $course->announcement()->pluck('announcements.id')->toArray();
            foreach ($announcement_id as $id){
                array_push($announcements_id, $id);
            }
        }
        $announcements_id = array_unique($announcements_id);

        //反向查詢
        $announcements = Announcement::whereIn('id', $announcements_id)->orderBy('priority')->orderBy('updated_at', 'desc')->paginate(5);

        foreach($announcements as $announcement){
            $courses_id = $announcement->course()->pluck('courses.id');

            $courses = Course::whereIn('id', $courses_id)->get();

            //查詢共同課程的名稱
            $common_courses_name = collect();
            foreach($courses as $course){
                $common_course_name = $course->common_course()->value('name');
                $common_courses_name->push($common_course_name);
            }
            $announcement->common_courses_name = $common_courses_name;
        }

        return view('announcement.showAnnouncement', [
            'announcements' => $announcements,
        ]);
    }


    //附件
    //刪除
    public function getDeleteAttachment(Request $request){
        $announcement_id = $request->get('announcement_id');

        $filename = $request->get('filename');

        $filepath = 'public/sys_announcement/'.$announcement_id.'/'.$filename;

        Storage::delete($filepath);

        $output = array(
            'filepath' => $filepath,
        );

        echo json_encode($output);
    }

    //下載
    public function downloadAttachment($first, $second, $third, $fourth){
//        return Storage::download('public/505102236/1/midterm.py');

        $filepath = $first.'/'.$second.'/'.$third.'/'.$fourth;
//        return Storage::download($filepath);

        return response()->download(storage_path().'/app/'.$filepath);
    }

    public function downloadAttachment_Announcement($id, $fileName){

        $filepath = $id.'/'.$fileName;

        return response()->download(storage_path().'/app/public/sys_announcement/'.$filepath);
    }
}
