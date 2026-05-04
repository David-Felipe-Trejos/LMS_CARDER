@extends('layouts.app')
@section('title','Crear Curso')@section('page-title','Nuevo Curso')
@section('content')
<div class="row justify-content-center"><div class="col-lg-8">
<div class="d-flex align-items-center gap-3 mb-4">
<a href="{{ route('courses.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
<h4 class="fw-bold mb-0">Crear Nuevo Curso</h4>
</div>
<div class="card"><div class="card-body p-4">
<form method="POST" action="{{ route('courses.store') }}" enctype="multipart/form-data">
@csrf
<div class="row g-3">
<div class="col-12"><label class="form-label fw-semibold">Título *</label><input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required placeholder="Ej: Gestión del Recurso Hídrico en Risaralda">@error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
<div class="col-12"><label class="form-label fw-semibold">Descripción</label><textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea></div>
<div class="col-md-5"><label class="form-label fw-semibold">Categoría</label><select name="category" class="form-select"><option value="">Sin categoría</option>@foreach(['hidrico'=>'Recurso Hídrico','biodiversidad'=>'Biodiversidad','cambio_climatico'=>'Cambio Climático','educacion_ambiental'=>'Educación Ambiental','gestion_riesgo'=>'Gestión del Riesgo','normatividad'=>'Normatividad','general'=>'General'] as $k=>$v)<option value="{{ $k }}" {{ old('category')===$k?'selected':'' }}>{{ $v }}</option>@endforeach</select></div>
<div class="col-md-3"><label class="form-label fw-semibold">Duración (h) *</label><input type="number" name="duration_hours" class="form-control" value="{{ old('duration_hours',8) }}" min="1" required></div>
<div class="col-md-4"><label class="form-label fw-semibold">Estado *</label><select name="status" class="form-select" required><option value="draft">Borrador</option><option value="published">Publicado</option></select></div>
@role('admin')
<div class="col-12"><label class="form-label fw-semibold">Instructor</label><select name="instructor_id" class="form-select">@foreach($instructors as $i)<option value="{{ $i->id }}">{{ $i->name }}</option>@endforeach</select></div>
@endrole
<div class="col-12"><label class="form-label fw-semibold">Imagen de portada</label><input type="file" name="cover_image" class="form-control" accept="image/*"><div class="form-text">Opcional. Máx. 2MB</div></div>
</div>
<hr class="my-4">
<div class="d-flex gap-2"><button type="submit" class="btn btn-carder px-4"><i class="bi bi-save me-1"></i>Crear Curso</button><a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">Cancelar</a></div>
</form>
</div></div>
</div></div>
@endsection
