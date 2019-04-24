<?php

namespace App\Http\Controllers;

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

    public function getDeleteSystemAnnouncement($id){
        DB::table('system_announcement')
            ->where('id', $id)
            ->delete();

        $filepath = 'public/sys_announcement/'.$id;
        Storage::deleteDirectory($filepath);

        return redirect()->back()->with('message', '刪除成功!');
    }

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
