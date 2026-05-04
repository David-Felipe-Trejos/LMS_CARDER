<?php
namespace App\Policies;
use App\Models\{Course, User};
class CoursePolicy {
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Course $course): bool { return true; }
    public function create(User $user): bool { return $user->hasRole(['admin','instructor']); }
    public function update(User $user, Course $course): bool {
        return $user->hasRole('admin') || ($user->hasRole('instructor') && $course->instructor_id === $user->id);
    }
    public function delete(User $user, Course $course): bool {
        return $user->hasRole('admin') || ($user->hasRole('instructor') && $course->instructor_id === $user->id);
    }
}
