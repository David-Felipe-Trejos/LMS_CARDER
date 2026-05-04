@extends('layouts.app')
@section('title','Cursos')@section('page-title','Cursos')
@section('content')
<div class="card mb-4"><div class="card-body p-3"><form method="GET" class="row g-2 align-items-end">
<div class="col-md-5"><input type="text" name="search" class="form-control form-control-sm" placeholder="Buscar..." value="{{ request('search') }}"></div>
<div class="col-md-4"><select name="category" class="form-select form-select-sm"><option value="">Todas las categorías</option>@foreach($categories as $k=>$v)<option value="{{ $k }}" {{ request('category')===$k?'selected':'' }}>{{ $v }}</option>@endforeach</select></div>
<div class="col-md-3 d-flex gap-2"><button class="btn btn-sm btn-carder flex-grow-1"><i class="bi bi-search me-1"></i>Buscar</button>@if(request()->hasAny(['search','category']))<a href="{{ route('courses.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x-lg"></i></a>@endif</div>
</form></div></div>
<div class="d-flex justify-content-between align-items-center mb-3">
<span class="text-muted" style="font-size:.84rem">{{ $courses->total() }} cursos</span>
@can('create',App\Models\Course::class)<a href="{{ route('courses.create') }}" class="btn btn-sm btn-carder"><i class="bi bi-plus-circle me-1"></i>Nuevo Curso</a>@endcan
</div>
@if($courses->count())
<div class="row g-3 mb-4">@foreach($courses as $course)
<div class="col-md-6 col-lg-4"><div class="course-card">
<div style="height:110px;background:linear-gradient(135deg,{{ $course->category_color }},{{ $course->category_color }}99);display:flex;align-items:center;justify-content:center;position:relative">
<i class="bi bi-book-half text-white" style="font-size:3rem;opacity:.3"></i>
<span style="position:absolute;top:.5rem;left:.5rem;background:rgba(0,0,0,.35);color:#fff;border-radius:20px;padding:.15rem .6rem;font-size:.68rem">{{ $course->category_label }}</span>
<span style="position:absolute;top:.5rem;right:.5rem;background:rgba(0,0,0,.35);color:#fff;border-radius:20px;padding:.15rem .6rem;font-size:.68rem">{{ $course->duration_hours }}h</span>
</div>
<div class="p-3 d-flex flex-column" style="flex:1">
<h6 class="fw-bold mb-1" style="font-size:.92rem">{{ Str::limit($course->title,55) }}</h6>
<p class="text-muted mb-2" style="font-size:.78rem;flex:1">{{ Str::limit($course->description,80) }}</p>
<div class="d-flex align-items-center gap-2 mb-3" style="font-size:.75rem;color:#64748b">
<div style="width:22px;height:22px;background:#e8f5ee;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0"><span class="fw-bold text-success" style="font-size:.62rem">{{ $course->instructor->initial }}</span></div>
<span class="text-truncate">{{ $course->instructor->name }}</span>
<span class="ms-auto flex-shrink-0"><i class="bi bi-people me-1"></i>{{ $course->enrollments_count }}</span>
</div>
<div class="d-flex gap-1">
<a href="{{ route('courses.show',$course) }}" class="btn btn-sm btn-outline-success flex-grow-1">Ver Curso</a>
@can('update',$course)<a href="{{ route('courses.edit',$course) }}" class="btn btn-sm btn-outline-primary" title="Editar"><i class="bi bi-pencil"></i></a>@endcan
</div>
</div>
</div></div>
@endforeach</div>
{{ $courses->links() }}
@else
<div class="text-center py-5 text-muted"><i class="bi bi-collection-play d-block mb-3" style="font-size:3rem"></i><h5>No se encontraron cursos</h5></div>
@endif
@endsection
