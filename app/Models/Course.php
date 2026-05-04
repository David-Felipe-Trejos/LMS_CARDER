<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Course extends Model {
    protected $fillable = ['instructor_id','title','description','category','duration_hours','status','cover_image'];
    public function instructor()   { return $this->belongsTo(User::class,'instructor_id'); }
    public function modules()      { return $this->hasMany(CourseModule::class)->orderBy('order'); }
    public function quizzes()      { return $this->hasMany(Quiz::class); }
    public function enrollments()  { return $this->hasMany(Enrollment::class); }
    public function certificates() { return $this->hasMany(Certificate::class); }
    public function lessons()      { return $this->hasManyThrough(Lesson::class,CourseModule::class,'course_id','module_id'); }
    public function getProgressForUser(int $userId): int {
        $total = $this->lessons()->count();
        if ($total === 0) return 0;
        $done = LessonProgress::where('user_id',$userId)->whereIn('lesson_id',$this->lessons()->pluck('lessons.id'))->where('completed',true)->count();
        return (int) round(($done / $total) * 100);
    }
    public function isEnrolled(int $userId): bool { return $this->enrollments()->where('user_id',$userId)->exists(); }
    public function getCategoryLabelAttribute(): string {
        return ['hidrico'=>'Recurso Hídrico','biodiversidad'=>'Biodiversidad','cambio_climatico'=>'Cambio Climático','educacion_ambiental'=>'Educación Ambiental','gestion_riesgo'=>'Gestión del Riesgo','normatividad'=>'Normatividad','general'=>'General'][$this->category] ?? ucfirst($this->category ?? 'Sin categoría');
    }
    public function getCategoryColorAttribute(): string {
        return ['hidrico'=>'#1a6fa0','biodiversidad'=>'#2d8a4e','cambio_climatico'=>'#e07b39','educacion_ambiental'=>'#6a9a32','gestion_riesgo'=>'#c0392b','normatividad'=>'#8e44ad','general'=>'#555555'][$this->category] ?? '#1e6b3a';
    }
}
