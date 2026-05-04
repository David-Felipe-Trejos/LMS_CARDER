@extends('layouts.app')
@section('title','Nuevo Usuario')
@section('page-title','Crear Usuario')
@section('content')
<div class="row justify-content-center"><div class="col-lg-7">
<div class="d-flex align-items-center gap-3 mb-4">
  <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
  <h4 class="fw-bold mb-0">Crear Nuevo Usuario</h4>
</div>
<div class="card">
  <div class="card-body p-4">
    <form method="POST" action="{{ route('users.store') }}">
      @csrf
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label fw-semibold">Nombre Completo *</label>
          <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                 value="{{ old('name') }}" required>
          @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Correo Electrónico *</label>
          <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                 value="{{ old('email') }}" required placeholder="usuario@carder.gov.co">
          @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Contraseña *</label>
          <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                 required minlength="8" placeholder="Mínimo 8 caracteres">
          @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Confirmar Contraseña *</label>
          <input type="password" name="password_confirmation" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Rol *</label>
          <select name="role" class="form-select @error('role') is-invalid @enderror" required>
            <option value="">Seleccionar rol...</option>
            @foreach($roles as $r)
            <option value="{{ $r->name }}" {{ old('role')===$r->name?'selected':'' }}>{{ ucfirst($r->name) }}</option>
            @endforeach
          </select>
          @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Teléfono</label>
          <input type="text" name="telefono" class="form-control" value="{{ old('telefono') }}" placeholder="314 000 0000">
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Cargo en CARDER</label>
          <input type="text" name="cargo" class="form-control" value="{{ old('cargo') }}" placeholder="Ej: Profesional Ambiental">
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Dependencia / Área</label>
          <input type="text" name="dependencia" class="form-control" value="{{ old('dependencia') }}" placeholder="Ej: Dirección de EA">
        </div>
      </div>
      <hr class="my-4">
      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-carder px-4"><i class="bi bi-person-plus me-1"></i>Crear Usuario</button>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>
</div></div>
@endsection
