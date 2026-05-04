<?php
namespace App\Http\Controllers;
use App\Models\{Course,CourseModule,Lesson,LessonProgress,Certificate,Enrollment};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
class LessonController extends Controller {
    public function store(Request $request, Course $course, CourseModule $module) {
        $this->authorize('update',$course);
        $v=$request->validate(['title'=>'required|string|max:255','type'=>'required|in:text,video,pdf,link','content'=>'required|string','duration_minutes'=>'nullable|integer|min:1']);
        $module->lessons()->create(['title'=>$v['title'],'type'=>$v['type'],'content'=>$v['content'],'duration_minutes'=>$v['duration_minutes']??null,'order'=>$module->lessons()->max('order')+1]);
        return back()->with('success','✅ Lección agregada.');
    }
    public function show(Course $course, Lesson $lesson) {
        $user=Auth::user();
        abort_unless($user->hasRole(['admin','instructor']) || $course->isEnrolled($user->id), 403, 'Debes estar inscrito para ver esta lección.');
        $lesson->load('module');
        LessonProgress::updateOrCreate(['user_id'=>$user->id,'lesson_id'=>$lesson->id],['completed'=>true,'viewed_at'=>now()]);
        $this->cert($course,$user->id);
        $all=$lesson->module->lessons; $ci=$all->search(fn($l)=>$l->id===$lesson->id);
        $prev=$ci>0?$all[$ci-1]:null; $next=$ci<$all->count()-1?$all[$ci+1]:null;
        $progress=$course->getProgressForUser($user->id);
        return view('lessons.show', compact('course','lesson','prev','next','progress'));
    }
    public function markComplete(Request $request, Course $course, Lesson $lesson) {
        LessonProgress::updateOrCreate(['user_id'=>Auth::id(),'lesson_id'=>$lesson->id],['completed'=>true,'viewed_at'=>now()]);
        $this->cert($course,Auth::id());
        return response()->json(['success'=>true,'progress'=>$course->getProgressForUser(Auth::id())]);
    }
    public function destroy(Course $course, Lesson $lesson) { $this->authorize('update',$course); $lesson->delete(); return back()->with('success','🗑️ Lección eliminada.'); }
    private function cert(Course $course, int $userId): void {
        if ($course->getProgressForUser($userId)<100) return;
        if (!$course->quizzes->every(fn($q)=>$q->hasPassed($userId))) return;
        if (Certificate::where('user_id',$userId)->where('course_id',$course->id)->exists()) return;
        Certificate::create(['user_id'=>$userId,'course_id'=>$course->id,'certificate_code'=>'CARDER-'.strtoupper(Str::random(8)),'issued_at'=>now()]);
        Enrollment::where('user_id',$userId)->where('course_id',$course->id)->update(['status'=>'completed','completed_at'=>now()]);
    }
}
