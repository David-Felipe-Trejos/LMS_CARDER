@extends('layouts.app')
@section('title','Mi Aprendizaje')@section('page-title','Mi Aprendizaje')
@section('content')
<div class="row g-3 mb-4">
<div class="col-6 col-md-3"><div class="stat-card" style="background:linear-gradient(135deg,#1e6b3a,#28a745)"><div class="stat-number">{{ $enrollments->count() }}</div><div class="stat-label">Inscritos</div><i class="bi bi-journal-check stat-icon"></i></div></div>
<div class="col-6 col-md-3"><div class="stat-card" style="background:linear-gradient(135deg,#1a4d6e,#0d6efd)"><div class="stat-number">{{ $enrollments->where('status','completed')->count() }}</div><div class="stat-label">Completados</div><i class="bi bi-check-circle-fill stat-icon"></i></div></div>
<div class="col-6 col-md-3"><div class="stat-card" style="background:linear-gradient(135deg,#7c3d00,#fd7e14)"><div class="stat-number">{{ auth()->user()->certificates()->count() }}</div><div class="stat-label">Certificados</div><i class="bi bi-award-fill stat-icon"></i></div></div>
<div class="col-6 col-md-3"><div class="stat-card" style="background:linear-gradient(135deg,#4a0072,#a855f7)"><div class="stat-number">{{ $enrollments->where('status','active')->count() }}</div><div class="stat-label">En Progreso</div><i class="bi bi-clock-fill stat-icon"></i></div></div>
</div>
<div class="row g-4">
<div class="col-lg-8">
@if($enrollments->count())
<div class="card mb-4"><div class="card-header d-flex justify-content-between align-items-center"><span><i class="bi bi-journal-bookmark me-2 text-success"></i>Mis Cursos</span><a href="{{ route('courses.index') }}" class="btn btn-sm btn-outline-success">Ver catálogo</a></div><div class="card-body p-0">
@foreach($enrollments as $e)
<div class="px-4 py-3 border-bottom"><div class="d-flex align-items-center gap-3 mb-2">
<div style="width:42px;height:42px;border-radius:8px;background:{{ $e->course->category_color }};display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="bi bi-book text-white"></i></div>
<div class="flex-grow-1 overflow-hidden"><a href="{{ route('courses.show',$e->course) }}" class="fw-semibold text-dark text-decoration-none d-block text-truncate" style="font-size:.9rem">{{ $e->course->title }}</a><span class="text-muted" style="font-size:.75rem">{{ $e->course->instructor->name }} · {{ $e->course->duration_hours }}h</span></div>
<span class="fw-bold {{ $e->progress>=100?'text-success':'text-primary' }}" style="font-size:.9rem;flex-shrink:0">{{ $e->progress }}%</span>
</div><div class="progress"><div class="progress-bar {{ $e->progress>=100?'bg-success':'' }}" style="width:{{ $e->progress }}%"></div></div></div>
@endforeach
</div></div>
@endif
@php $certs=auth()->user()->certificates()->with('course')->get(); @endphp
@if($certs->count())
<div class="card"><div class="card-header"><i class="bi bi-award me-2 text-warning"></i>Mis Certificados</div><div class="card-body">
@foreach($certs as $cert)
<div class="d-flex align-items-center gap-3 p-2 mb-2" style="background:#fffdf0;border:1px solid #ffc107;border-radius:10px">
<i class="bi bi-award-fill text-warning fs-3 flex-shrink-0"></i>
<div class="flex-grow-1"><div class="fw-semibold" style="font-size:.88rem">{{ $cert->course->title }}</div><div style="font-family:monospace;font-size:.78rem;color:#856404">{{ $cert->certificate_code }}</div></div>
<a href="{{ route('courses.certificate',$cert->course) }}" class="btn btn-sm btn-warning flex-shrink-0"><i class="bi bi-download me-1"></i>Ver</a>
</div>
@endforeach
</div></div>
@endif
</div>
<div class="col-lg-4">
@if($availableCourses->count())
<div class="card"><div class="card-header"><i class="bi bi-collection-play me-2 text-primary"></i>Cursos Disponibles</div><div class="card-body p-0">
@foreach($availableCourses as $c)
<div class="d-flex align-items-center gap-3 px-3 py-3 border-bottom">
<div style="width:36px;height:36px;border-radius:8px;background:{{ $c->category_color }};display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="bi bi-book text-white" style="font-size:.85rem"></i></div>
<div class="flex-grow-1 overflow-hidden"><div class="fw-semibold text-truncate" style="font-size:.83rem">{{ $c->title }}</div><div class="text-muted" style="font-size:.72rem">{{ $c->instructor->name }}</div></div>
<form method="POST" action="{{ route('courses.enroll',$c) }}">@csrf<button class="btn btn-sm btn-outline-success flex-shrink-0" title="Inscribirse"><i class="bi bi-plus-circle"></i></button></form>
</div>
@endforeach
<div class="p-3 text-center"><a href="{{ route('courses.index') }}" class="btn btn-sm btn-carder w-100">Ver todos</a></div>
</div></div>
@endif
</div>
</div>
@endsection
