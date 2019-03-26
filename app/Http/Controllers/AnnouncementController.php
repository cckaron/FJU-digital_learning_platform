<?php

namespace App\Http\Controllers;

use App\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
}
