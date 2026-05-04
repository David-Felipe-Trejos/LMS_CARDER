<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Quiz extends Model {
    protected $fillable = ['course_id','title','description','passing_score','max_attempts'];
    public function course()    { return $this->belongsTo(Course::class); }
    public function questions() { return $this->hasMany(Question::class)->orderBy('order'); }
    public function attempts()  { return $this->hasMany(QuizAttempt::class); }
    public function getAttemptsCountForUser(int $userId): int { return $this->attempts()->where('user_id',$userId)->count(); }
    public function hasPassed(int $userId): bool { return $this->attempts()->where('user_id',$userId)->where('passed',true)->exists(); }
}
