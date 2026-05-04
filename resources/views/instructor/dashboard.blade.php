@extends('layouts.app')
@section('title','Panel Instructor')@section('page-title','Panel del Instructor')
@section('content')
<div class="row g-3 mb-4">
<div class="col-6 col-md-3"><div class="stat-card" style="background:linear-gradient(135deg,#1e6b3a,#28a745)"><div class="stat-number">{{ $courses->count() }}</div><div class="stat-label">Mis Cursos</div><i class="bi bi-mortarboard-fill stat-icon"></i></div></div>
<div class="col-6 col-md-3"><div class="stat-card" style="background:linear-gradient(135deg,#1a4d6e,#0d6efd)"><div class="stat-number">{{ $courses->sum('enrollments_count') }}</div><div class="stat-label">Total Participantes</div><i class="bi bi-people-fill stat-icon"></i></div></div>
<div class="col-6 col-md-3"><div class="stat-card" style="background:linear-gradient(135deg,#7c3d00,#fd7e14)"><div class="stat-number">{{ $courses->where('status','published')->count() }}</div><div class="stat-label">Publicados</div><i class="bi bi-eye-fill stat-icon"></i></div></div>
<div class="col-6 col-md-3"><div class="stat-card" style="background:linear-gradient(135deg,#4a0072,#a855f7)"><div class="stat-number">{{ $courses->where('status','draft')->count() }}</div><div class="stat-label">Borradores</div><i class="bi bi-pencil-fill stat-icon"></i></div></div>
</div>
<div class="d-flex gap-2 mb-4">
<a href="{{ route('courses.create') }}" class="btn btn-carder"><i class="bi bi-plus-circle me-1"></i>Nuevo Curso</a>
<a href="{{ route('reports.index') }}" class="btn btn-outline-success"><i class="bi bi-bar-chart me-1"></i>Ver Reportes</a>
</div>
<div class="card"><div class="card-header"><i class="bi bi-mortarboard me-2 text-success"></i>Mis Cursos</div><div class="card-body p-0">
@forelse($courses as $course)
<div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
<div style="width:44px;height:44px;border-radius:10px;background:{{ $course->category_color }};display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="bi bi-book text-white fs-5"></i></div>
<div class="flex-grow-1 overflow-hidden"><a href="{{ route('courses.show',$course) }}" class="fw-semibold text-dark text-decoration-none d-block text-truncate" style="font-size:.9rem">{{ $course->title }}</a><span class="text-muted" style="font-size:.78rem"><i class="bi bi-people me-1"></i>{{ $course->enrollments_count }} participantes &nbsp;·&nbsp;<i class="bi bi-clock me-1"></i>{{ $course->duration_hours }}h</span></div>
@php $sc=['draft'=>'secondary','published'=>'success','archived'=>'warning'][$course->status]; @endphp
<span class="badge text-bg-{{ $sc }}" style="font-size:.7rem;flex-shrink:0">{{ ucfirst($course->status) }}</span>
<div class="d-flex gap-1 flex-shrink-0"><a href="{{ route('courses.show',$course) }}" class="btn btn-sm btn-outline-secondary" title="Ver"><i class="bi bi-eye"></i></a><a href="{{ route('courses.edit',$course) }}" class="btn btn-sm btn-outline-primary" title="Editar"><i class="bi bi-pencil"></i></a></div>
</div>
@empty
<div class="text-center py-5 text-muted"><i class="bi bi-mortarboard d-block mb-3 fs-1"></i><p>No has creado ningún curso aún.</p><a href="{{ route('courses.create') }}" class="btn btn-carder btn-sm">Crear primer curso</a></div>
@endforelse
</div></div>
@endsection
