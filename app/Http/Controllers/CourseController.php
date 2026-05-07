<?php
namespace App\Http\Controllers;

use App\Models\{Course, Enrollment, Certificate, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Storage};

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::with('instructor')->withCount('enrollments');

        if (Auth::user()->hasRole('instructor') && !Auth::user()->hasRole('admin')) {
            $query->where('instructor_id', Auth::id());
        } elseif (Auth::user()->hasRole('participant')) {
            $query->where('status', 'published');
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('title', 'like', "%$s%")
                                      ->orWhere('description', 'like', "%$s%"));
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $courses = $query->latest()->paginate(12)->appends($request->query());

        $categories = [
            'hidrico'              => 'Recurso Hídrico',
            'biodiversidad'        => 'Biodiversidad',
            'cambio_climatico'     => 'Cambio Climático',
            'educacion_ambiental'  => 'Educación Ambiental',
            'gestion_riesgo'       => 'Gestión del Riesgo',
            'normatividad'         => 'Normatividad',
            'general'              => 'General',
        ];

        return view('courses.index', compact('courses', 'categories'));
    }

    public function create()
    {
        $this->authorize('create', Course::class);
        // Trae instructores Y admins (un admin también puede ser instructor de un curso).
        $instructors = User::role(['instructor', 'admin'])->orderBy('name')->get();
        return view('courses.create', compact('instructors'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Course::class);

        $data = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'category'       => 'nullable|string|max:100',
            'duration_hours' => 'required|integer|min:1|max:500',
            'status'         => 'required|in:draft,published,archived',
            'cover_image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'instructor_id'  => 'nullable|integer|exists:users,id',
        ]);

        // Asignación segura del instructor.
        if (Auth::user()->hasRole('admin') && !empty($data['instructor_id'])) {
            $data['instructor_id'] = $data['instructor_id'];
        } else {
            $data['instructor_id'] = Auth::id();
        }

        // Manejo seguro del archivo.
        if ($request->hasFile('cover_image') && $request->file('cover_image')->isValid()) {
            $data['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        $course = Course::create($data);

        return redirect()->route('courses.show', $course)
            ->with('success', '✅ Curso creado exitosamente.');
    }

    public function show(Course $course)
    {
        $course->load('instructor', 'modules.lessons', 'quizzes');
        $user     = Auth::user();
        $enrolled = $course->isEnrolled($user->id);
        $cert     = Certificate::where('user_id', $user->id)
                               ->where('course_id', $course->id)->first();
        $progress = $course->getProgressForUser($user->id);
        return view('courses.show', compact('course', 'enrolled', 'cert', 'progress'));
    }

    public function edit(Course $course)
    {
        $this->authorize('update', $course);
        $instructors = User::role(['instructor', 'admin'])->orderBy('name')->get();
        return view('courses.edit', compact('course', 'instructors'));
    }

    public function update(Request $request, Course $course)
    {
        $this->authorize('update', $course);

        $data = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'category'       => 'nullable|string|max:100',
            'duration_hours' => 'required|integer|min:1|max:500',
            'status'         => 'required|in:draft,published,archived',
            'cover_image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'instructor_id'  => 'nullable|integer|exists:users,id',
        ]);

        if ($request->hasFile('cover_image') && $request->file('cover_image')->isValid()) {
            if ($course->cover_image) {
                Storage::disk('public')->delete($course->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        if (Auth::user()->hasRole('admin') && !empty($data['instructor_id'])) {
            // Permite que el admin reasigne instructor.
        } else {
            unset($data['instructor_id']);
        }

        $course->update($data);

        return redirect()->route('courses.show', $course)
            ->with('success', '✅ Curso actualizado.');
    }

    public function destroy(Course $course)
    {
        $this->authorize('delete', $course);
        if ($course->cover_image) {
            Storage::disk('public')->delete($course->cover_image);
        }
        $course->delete();
        return redirect()->route('courses.index')->with('success', '🗑️ Curso eliminado.');
    }

    public function enroll(Course $course)
    {
        $user = Auth::user();
        if (!$user->hasRole('participant')) {
            return back()->with('error', 'Solo los participantes pueden inscribirse.');
        }
        if ($course->isEnrolled($user->id)) {
            return back()->with('error', 'Ya estás inscrito en este curso.');
        }
        if ($course->status !== 'published') {
            return back()->with('error', 'Este curso no está disponible.');
        }
        Enrollment::create([
            'user_id'     => $user->id,
            'course_id'   => $course->id,
            'status'      => 'active',
            'enrolled_at' => now(),
        ]);
        return back()->with('success', '✅ ¡Te has inscrito exitosamente!');
    }

    public function certificate(Course $course)
    {
        $user = Auth::user();
        $cert = Certificate::where('user_id', $user->id)
                           ->where('course_id', $course->id)
                           ->firstOrFail();
        return view('courses.certificate', compact('course', 'cert', 'user'));
    }
}
