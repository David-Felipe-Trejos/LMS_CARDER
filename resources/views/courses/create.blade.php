@extends('layouts.app')
@section('title','Crear Curso')
@section('page-title','Nuevo Curso')
 
@section('content')
<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="d-flex align-items-center gap-3 mb-4">
      <a href="{{ route('courses.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
      </a>
      <h4 class="fw-bold mb-0">Crear Nuevo Curso</h4>
    </div>
 
    <div class="card">
      <div class="card-body p-4">
        <form method="POST" action="{{ route('courses.store') }}" enctype="multipart/form-data">
          @csrf
 
          <div class="row g-3">
            {{-- Título --}}
            <div class="col-12">
              <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
              <input type="text" name="title"
                     class="form-control @error('title') is-invalid @enderror"
                     value="{{ old('title') }}" required maxlength="255"
                     placeholder="Ej: Gestión del Recurso Hídrico en Risaralda">
              @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
 
            {{-- Descripción --}}
            <div class="col-12">
              <label class="form-label fw-semibold">Descripción</label>
              <textarea name="description" rows="4"
                        class="form-control @error('description') is-invalid @enderror"
                        placeholder="Breve descripción del contenido y objetivos del curso...">{{ old('description') }}</textarea>
              @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
 
            {{-- Categoría --}}
            <div class="col-md-5">
              <label class="form-label fw-semibold">Categoría</label>
              <select name="category" class="form-select @error('category') is-invalid @enderror">
                <option value="">Sin categoría</option>
                @foreach([
                  'hidrico'             => 'Recurso Hídrico',
                  'biodiversidad'       => 'Biodiversidad',
                  'cambio_climatico'    => 'Cambio Climático',
                  'educacion_ambiental' => 'Educación Ambiental',
                  'gestion_riesgo'      => 'Gestión del Riesgo',
                  'normatividad'        => 'Normatividad',
                  'general'             => 'General',
                ] as $k => $v)
                  <option value="{{ $k }}" {{ old('category') === $k ? 'selected' : '' }}>{{ $v }}</option>
                @endforeach
              </select>
              @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
 
            {{-- Duración --}}
            <div class="col-md-3">
              <label class="form-label fw-semibold">Duración (h) <span class="text-danger">*</span></label>
              <input type="number" name="duration_hours"
                     class="form-control @error('duration_hours') is-invalid @enderror"
                     value="{{ old('duration_hours', 8) }}" min="1" max="500" required>
              @error('duration_hours')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
 
            {{-- Estado --}}
            <div class="col-md-4">
              <label class="form-label fw-semibold">Estado <span class="text-danger">*</span></label>
              <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                <option value="draft"     {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Borrador</option>
                <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Publicado</option>
                <option value="archived"  {{ old('status') === 'archived' ? 'selected' : '' }}>Archivado</option>
              </select>
              @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
 
            {{-- Instructor (solo admin) --}}
            @role('admin')
            <div class="col-12">
              <label class="form-label fw-semibold">Instructor</label>
              @if($instructors->isEmpty())
                <div class="alert alert-warning py-2 mb-0">
                  <i class="bi bi-exclamation-triangle me-1"></i>
                  No hay instructores registrados. El curso quedará asignado a tu usuario.
                </div>
              @else
                <select name="instructor_id" class="form-select @error('instructor_id') is-invalid @enderror">
                  <option value="">— Asignarme a mí —</option>
                  @foreach($instructors as $i)
                    <option value="{{ $i->id }}" {{ (int) old('instructor_id') === $i->id ? 'selected' : '' }}>
                      {{ $i->name }} ({{ $i->email }})
                    </option>
                  @endforeach
                </select>
                @error('instructor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
              @endif
            </div>
            @endrole
 
            {{-- Imagen --}}
            <div class="col-12">
              <label class="form-label fw-semibold">Imagen de portada</label>
              <input type="file" name="cover_image"
                     class="form-control @error('cover_image') is-invalid @enderror"
                     accept="image/jpeg,image/png,image/webp">
              <div class="form-text">Opcional. JPG, PNG o WEBP. Máx. 2MB.</div>
              @error('cover_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
 
          <hr class="my-4">
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-carder px-4">
              <i class="bi bi-save me-1"></i>Crear Curso
            </button>
            <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">Cancelar</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection