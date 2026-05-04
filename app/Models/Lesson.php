<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Lesson extends Model {
    protected $fillable = ['module_id','title','content','type','duration_minutes','order'];
    public function module()   { return $this->belongsTo(CourseModule::class,'module_id'); }
    public function progress() { return $this->hasMany(LessonProgress::class); }
    public function isCompletedByUser(int $userId): bool { return $this->progress()->where('user_id',$userId)->where('completed',true)->exists(); }
}
