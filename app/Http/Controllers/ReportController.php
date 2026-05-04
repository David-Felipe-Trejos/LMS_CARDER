<?php
namespace App\Http\Controllers;
use App\Models\{Course,User,Enrollment,QuizAttempt,Certificate};
use Illuminate\Support\Facades\Auth;
class ReportController extends Controller {
    public function index() {
        $user=Auth::user();
        if ($user->hasRole('admin')) {
            $courses=Course::withCount(['enrollments','enrollments as completed_count'=>fn($q)=>$q->where('status','completed')])->with('instructor','certificates')->get()->map(function($c){$c->completion_rate=$c->enrollments_count>0?round(($c->completed_count/$c->enrollments_count)*100):0;return $c;});
            $stats=['total_users'=>User::count(),'total_courses'=>Course::count(),'total_enrollments'=>Enrollment::count(),'total_certs'=>Certificate::count(),'pass_rate'=>$this->pr()];
            $monthly=Enrollment::selectRaw('MONTH(created_at) as month, COUNT(*) as total')->whereYear('created_at',date('Y'))->groupBy('month')->orderBy('month')->get();
            return view('reports.admin', compact('courses','stats','monthly'));
        }
        $courses=Course::where('instructor_id',$user->id)->withCount(['enrollments','enrollments as completed_count'=>fn($q)=>$q->where('status','completed')])->get()->map(function($c){$c->completion_rate=$c->enrollments_count>0?round(($c->completed_count/$c->enrollments_count)*100):0;return $c;});
        $attempts=QuizAttempt::whereIn('quiz_id',\App\Models\Quiz::whereIn('course_id',$courses->pluck('id'))->pluck('id'))->with('user','quiz.course')->latest()->take(30)->get();
        return view('reports.instructor', compact('courses','attempts'));
    }
    public function exportPdf() {
        $courses=Course::withCount('enrollments')->with('instructor')->get();
        $stats=['total_courses'=>Course::count(),'total_enrollments'=>Enrollment::count(),'total_certs'=>Certificate::count()];
        return view('reports.pdf', compact('courses','stats'));
    }
    private function pr(): float { $t=QuizAttempt::count(); $p=QuizAttempt::where('passed',true)->count(); return $t>0?round(($p/$t)*100,1):0; }
}
