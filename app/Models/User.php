<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
class User extends Authenticatable {
    use Notifiable, HasRoles;
    protected $fillable = ['name','email','password','cargo','dependencia','telefono','active','avatar'];
    protected $hidden   = ['password','remember_token'];
    protected $casts    = ['email_verified_at'=>'datetime','active'=>'boolean'];
    public function enrollments()       { return $this->hasMany(Enrollment::class); }
    public function instructorCourses() { return $this->hasMany(Course::class,'instructor_id'); }
    public function certificates()      { return $this->hasMany(Certificate::class); }
    public function quizAttempts()      { return $this->hasMany(QuizAttempt::class); }
    public function lessonProgress()    { return $this->hasMany(LessonProgress::class); }
    public function isEnrolledIn(Course $course): bool { return $this->enrollments()->where('course_id',$course->id)->exists(); }
    public function getInitialAttribute(): string { return strtoupper(substr($this->name,0,1)); }
}
