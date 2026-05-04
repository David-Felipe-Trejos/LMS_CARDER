<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class LessonProgress extends Model {
    protected $table = 'lesson_progress';
    protected $fillable = ['user_id','lesson_id','completed','viewed_at'];
    protected $casts = ['completed'=>'boolean','viewed_at'=>'datetime'];
    public function user()   { return $this->belongsTo(User::class); }
    public function lesson() { return $this->belongsTo(Lesson::class); }
}
