@extends('layouts.app')
@section('title','Editar Usuario')
@section('page-title','Editar Usuario')
@section('content')
<div class="row justify-content-center"><div class="col-lg-7">
<div class="d-flex align-items-center gap-3 mb-4">
  <a href="{{ route('users.show',$user) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
  <div>
    <h4 class="fw-bold mb-0">Editar Usuario</h4>
    <p class="text-muted mb-0" style="font-size:.82rem">{{ $user->name }}</p>
  </div>
</div>
<div class="card">
  <div class="card-body p-4">
    <form method="POST" action="{{ route('users.update',$user) }}">
      @csrf @method('PUT')
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label fw-semibold">Nombre Completo *</label>
          <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                 value="{{ old('name',$user->name) }}" required>
          @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Correo Electrónico *</label>
          <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                 value="{{ old('email',$user->email) }}" required>
          @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Rol *</label>
          <select name="role" class="form-select @error('role') is-invalid @enderror" required>
            @foreach($roles as $r)
            <option value="{{ $r->name }}" {{ old('role',$user->getRoleNames()->first())===$r->name?'selected':'' }}>{{ ucfirst($r->name) }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Teléfono</label>
          <input type="text" name="telefono" class="form-control" value="{{ old('telefono',$user->telefono) }}">
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Cargo</label>
          <input type="text" name="cargo" class="form-control" value="{{ old('cargo',$user->cargo) }}">
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Dependencia</label>
          <input type="text" name="dependencia" class="form-control" value="{{ old('dependencia',$user->dependencia) }}">
        </div>
        <div class="col-12">
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="active" id="activeCheck" value="1"
                   {{ old('active',$user->active)?'checked':'' }}>
            <label class="form-check-label fw-semibold" for="activeCheck">Usuario activo</label>
          </div>
        </div>
      </div>
      <hr class="my-4">
      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i>Guardar Cambios</button>
        <a href="{{ route('users.show',$user) }}" class="btn btn-outline-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>
</div></div>
@endsection
