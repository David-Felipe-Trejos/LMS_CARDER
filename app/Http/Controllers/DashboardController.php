<?php
namespace App\Http\Controllers;
use App\Models\{Course,Enrollment,User,Certificate};
use Illuminate\Support\Facades\Auth;
class DashboardController extends Controller {
    public function index() {
        $user = Auth::user();
        if ($user->hasRole('admin')) {
            return view('admin.dashboard', [
                'totalUsers'        => User::count(),
                'totalCourses'      => Course::count(),
                'totalEnrollments'  => Enrollment::count(),
                'totalCerts'        => Certificate::count(),
                'recentEnrollments' => Enrollment::with(['user','course'])->latest()->take(8)->get(),
                'courseStats'       => Course::withCount('enrollments')->with('instructor')->latest()->take(6)->get(),
                'monthlyData'       => Enrollment::selectRaw('MONTH(created_at) as month, COUNT(*) as total')->whereYear('created_at',date('Y'))->groupBy('month')->orderBy('month')->get(),
            ]);
        }
        if ($user->hasRole('instructor')) {
            $courses = Course::where('instructor_id',$user->id)->withCount('enrollments')->latest()->get();
            return view('instructor.dashboard', compact('courses'));
        }
        $enrollments = Enrollment::with(['course.instructor'])->where('user_id',$user->id)->latest()->get()->map(function($e) use ($user) { $e->progress=$e->course->getProgressForUser($user->id); return $e; });
        $availableCourses = Course::where('status','published')->whereNotIn('id',$enrollments->pluck('course_id'))->with('instructor')->take(6)->get();
        return view('participant.dashboard', compact('enrollments','availableCourses'));
    }
}
