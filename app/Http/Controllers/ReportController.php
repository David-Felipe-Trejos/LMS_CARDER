<?php
namespace App\Http\Controllers;
 
use App\Models\{Course, User, Enrollment, QuizAttempt, Certificate, Quiz};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
 
class ReportController extends Controller
{
    /**
     * Muestra el panel de reportes según el rol del usuario.
     */
    public function index()
    {
        $user = Auth::user();
 
        // ============ ADMIN ============
        if ($user->hasRole('admin')) {
            $courses = Course::withCount([
                    'enrollments',
                    'enrollments as completed_count' => fn($q) => $q->where('status', 'completed'),
                ])
                ->with('instructor', 'certificates')
                ->get()
                ->map(function ($c) {
                    $c->completion_rate = $c->enrollments_count > 0
                        ? round(($c->completed_count / $c->enrollments_count) * 100)
                        : 0;
                    return $c;
                });
 
            $stats = [
                'total_users'       => User::count(),
                'total_courses'     => Course::count(),
                'total_enrollments' => Enrollment::count(),
                'total_certs'       => Certificate::count(),
                'pass_rate'         => $this->passRate(),
            ];
 
            // Matrículas por mes del año actual.
            // ⚠️ La vista espera la variable $monthlyEnrollments (no $monthly).
            $monthlyEnrollments = Enrollment::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
                ->whereYear('created_at', date('Y'))
                ->groupBy('month')
                ->orderBy('month')
                ->get();
 
            return view('reports.admin', compact('courses', 'stats', 'monthlyEnrollments'));
        }
 
        // ============ INSTRUCTOR ============
        $courses = Course::where('instructor_id', $user->id)
            ->withCount([
                'enrollments',
                'enrollments as completed_count' => fn($q) => $q->where('status', 'completed'),
            ])
            ->get()
            ->map(function ($c) {
                $c->completion_rate = $c->enrollments_count > 0
                    ? round(($c->completed_count / $c->enrollments_count) * 100)
                    : 0;
                return $c;
            });
 
        $quizIds = Quiz::whereIn('course_id', $courses->pluck('id'))->pluck('id');
 
        $attempts = QuizAttempt::whereIn('quiz_id', $quizIds)
            ->with('user', 'quiz.course')
            ->latest()
            ->take(30)
            ->get();
 
        return view('reports.instructor', compact('courses', 'attempts'));
    }
 
    /**
     * Exporta un reporte en formato HTML imprimible (PDF vía navegador).
     */
    public function exportPdf()
    {
        $user = Auth::user();
 
        $coursesQuery = Course::withCount([
                'enrollments',
                'enrollments as completed_count' => fn($q) => $q->where('status', 'completed'),
            ])
            ->with('instructor', 'certificates');
 
        // Si es instructor, solo sus cursos.
        if ($user->hasRole('instructor') && !$user->hasRole('admin')) {
            $coursesQuery->where('instructor_id', $user->id);
        }
 
        $courses = $coursesQuery->get()->map(function ($c) {
            $c->completion_rate = $c->enrollments_count > 0
                ? round(($c->completed_count / $c->enrollments_count) * 100)
                : 0;
            return $c;
        });
 
        $stats = [
            'total_users'       => User::count(),
            'total_courses'     => Course::count(),
            'total_enrollments' => Enrollment::count(),
            'total_certs'       => Certificate::count(),
            'pass_rate'         => $this->passRate(),
        ];
 
        return view('reports.pdf', compact('courses', 'stats'));
    }
 
    /**
     * Calcula la tasa global de aprobación de evaluaciones.
     */
    private function passRate(): float
    {
        $total  = QuizAttempt::count();
        $passed = QuizAttempt::where('passed', true)->count();
        return $total > 0 ? round(($passed / $total) * 100, 1) : 0;
    }
}
 