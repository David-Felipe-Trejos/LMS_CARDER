<?php
namespace App\Http\Controllers;
use App\Models\{Course,Quiz,Question,QuestionOption,QuizAttempt,Certificate,Enrollment};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
class QuizController extends Controller {
    public function create(Course $course) { $this->authorize('update',$course); return view('quizzes.create', compact('course')); }
    public function store(Request $request, Course $course) {
        $this->authorize('update',$course);
        $data=$request->validate(['title'=>'required|string|max:255','description'=>'nullable|string','passing_score'=>'required|integer|min:1|max:100','max_attempts'=>'required|integer|min:1|max:10','questions'=>'required|array|min:1','questions.*.text'=>'required|string','questions.*.options'=>'required|array|min:2','questions.*.options.*'=>'required|string','questions.*.correct'=>'required|integer']);
        $quiz=$course->quizzes()->create(['title'=>$data['title'],'description'=>$data['description']??null,'passing_score'=>$data['passing_score'],'max_attempts'=>$data['max_attempts']]);
        foreach ($data['questions'] as $qi=>$qData) { $q=$quiz->questions()->create(['question_text'=>$qData['text'],'order'=>$qi+1]); foreach ($qData['options'] as $oi=>$opt) $q->options()->create(['option_text'=>$opt,'is_correct'=>$oi===(int)$qData['correct']]); }
        return redirect()->route('courses.show',$course)->with('success','✅ Evaluación creada.');
    }
    public function show(Course $course, Quiz $quiz) {
        $quiz->load('questions.options');
        $attempts=$quiz->getAttemptsCountForUser(Auth::id());
        $canAttempt=$attempts<$quiz->max_attempts && !$quiz->hasPassed(Auth::id());
        $lastAttempt=$quiz->attempts()->where('user_id',Auth::id())->latest()->first();
        return view('quizzes.show', compact('course','quiz','attempts','canAttempt','lastAttempt'));
    }
    public function submit(Request $request, Course $course, Quiz $quiz) {
        $user=Auth::user();
        if ($quiz->getAttemptsCountForUser($user->id)>=$quiz->max_attempts || $quiz->hasPassed($user->id)) return back()->with('error','No puedes realizar más intentos.');
        $quiz->load('questions.options');
        $answers=$request->input('answers',[]); $correct=0; $total=$quiz->questions->count(); $saved=[];
        foreach ($quiz->questions as $question) {
            $sid=$answers[$question->id]??null; $isC=false;
            if ($sid) { $opt=$question->options->firstWhere('id',$sid); $isC=$opt?->is_correct??false; if($isC) $correct++; }
            $saved[$question->id]=['selected'=>$sid,'correct'=>$isC];
        }
        $score=$total>0?(int)round(($correct/$total)*100):0; $passed=$score>=$quiz->passing_score;
        QuizAttempt::create(['user_id'=>$user->id,'quiz_id'=>$quiz->id,'score'=>$score,'passed'=>$passed,'answers'=>$saved,'started_at'=>now()->subMinutes(5),'finished_at'=>now()]);
        if ($passed) $this->cert($course,$user->id);
        return redirect()->route('quizzes.show',[$course,$quiz])->with('quiz_result',['score'=>$score,'passed'=>$passed,'correct'=>$correct,'total'=>$total]);
    }
    public function destroy(Course $course, Quiz $quiz) { $this->authorize('update',$course); $quiz->delete(); return back()->with('success','🗑️ Evaluación eliminada.'); }
    private function cert(Course $course, int $userId): void {
        if ($course->getProgressForUser($userId)<100) return;
        if (!$course->quizzes->every(fn($q)=>$q->hasPassed($userId))) return;
        if (Certificate::where('user_id',$userId)->where('course_id',$course->id)->exists()) return;
        Certificate::create(['user_id'=>$userId,'course_id'=>$course->id,'certificate_code'=>'CARDER-'.strtoupper(Str::random(8)),'issued_at'=>now()]);
        Enrollment::where('user_id',$userId)->where('course_id',$course->id)->update(['status'=>'completed','completed_at'=>now()]);
    }
}
