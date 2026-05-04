@extends('layouts.app')
@section('title','Mi Perfil')@section('page-title','Mi Perfil')
@section('content')
<div class="row justify-content-center"><div class="col-lg-8">
<div style="background:linear-gradient(135deg,#0d2d1a,#1a4d6e);border-radius:14px;padding:2rem;color:#fff;margin-bottom:1.5rem;display:flex;align-items:center;gap:1.5rem">
<div style="width:72px;height:72px;background:rgba(255,255,255,.15);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;border:3px solid rgba(255,255,255,.3)"><span style="font-family:'Nunito',sans-serif;font-weight:900;font-size:1.8rem;color:#fff">{{ auth()->user()->initial }}</span></div>
<div><h4 class="fw-bold mb-0" style="font-size:1.3rem">{{ $user->name }}</h4><div style="opacity:.75;font-size:.85rem">{{ $user->email }}</div><span style="background:rgba(255,255,255,.2);border-radius:20px;padding:.2rem .7rem;font-size:.72rem;font-weight:700;text-transform:capitalize;margin-top:.3rem;display:inline-block">{{ $user->getRoleNames()->first() }}</span></div>
</div>
<div class="row g-4">
<div class="col-md-7">
<div class="card"><div class="card-header"><i class="bi bi-person-fill me-2 text-success"></i>Datos Personales</div><div class="card-body p-4">
<form method="POST" action="{{ route('profile.update') }}">@csrf @method('PATCH')
<div class="mb-3"><label class="form-label fw-semibold" style="font-size:.84rem">Nombre Completo *</label><input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name',$user->name) }}" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
<div class="mb-3"><label class="form-label fw-semibold" style="font-size:.84rem">Correo Electrónico *</label><input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email',$user->email) }}" required>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
<div class="mb-3"><label class="form-label fw-semibold" style="font-size:.84rem">Cargo en CARDER</label><input type="text" name="cargo" class="form-control" value="{{ old('cargo',$user->cargo) }}" placeholder="Ej: Profesional Ambiental"></div>
<div class="mb-3"><label class="form-label fw-semibold" style="font-size:.84rem">Dependencia / Área</label><input type="text" name="dependencia" class="form-control" value="{{ old('dependencia',$user->dependencia) }}"></div>
<div class="mb-4"><label class="form-label fw-semibold" style="font-size:.84rem">Teléfono</label><input type="text" name="telefono" class="form-control" value="{{ old('telefono',$user->telefono) }}" placeholder="314 000 0000"></div>
<button type="submit" class="btn btn-carder px-4"><i class="bi bi-save me-1"></i>Guardar Cambios</button>
</form>
</div></div>
</div>
<div class="col-md-5">
<div class="card mb-4"><div class="card-header"><i class="bi bi-shield-lock me-2 text-warning"></i>Cambiar Contraseña</div><div class="card-body p-4">
<form method="POST" action="{{ route('profile.password') }}">@csrf @method('PUT')
<div class="mb-3"><label class="form-label fw-semibold" style="font-size:.84rem">Contraseña actual *</label><input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>@error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
<div class="mb-3"><label class="form-label fw-semibold" style="font-size:.84rem">Nueva contraseña *</label><input type="password" name="password" class="form-control @error('password') is-invalid @enderror" minlength="8" required placeholder="Mínimo 8 caracteres">@error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
<div class="mb-4"><label class="form-label fw-semibold" style="font-size:.84rem">Confirmar contraseña *</label><input type="password" name="password_confirmation" class="form-control" required></div>
<button type="submit" class="btn btn-warning px-4 fw-semibold"><i class="bi bi-key me-1"></i>Actualizar Contraseña</button>
</form>
</div></div>
</div>
</div>
</div></div>
@endsection
