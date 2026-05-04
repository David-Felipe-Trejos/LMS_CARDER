@extends('layouts.app')
@section('title','Usuarios')
@section('page-title','Gestión de Usuarios')
@section('content')

<div class="card mb-4">
  <div class="card-body p-3">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-5">
        <input type="text" name="search" class="form-control form-control-sm"
               placeholder="Buscar por nombre o email..." value="{{ request('search') }}">
      </div>
      <div class="col-md-3">
        <select name="role" class="form-select form-select-sm">
          <option value="">Todos los roles</option>
          @foreach($roles as $r)
          <option value="{{ $r->name }}" {{ request('role')===$r->name?'selected':'' }}>{{ ucfirst($r->name) }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4 d-flex gap-2">
        <button class="btn btn-sm btn-carder flex-grow-1"><i class="bi bi-search me-1"></i>Buscar</button>
        @if(request()->hasAny(['search','role']))
        <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
        @endif
        <a href="{{ route('users.create') }}" class="btn btn-sm btn-outline-success">
          <i class="bi bi-person-plus me-1"></i>Nuevo
        </a>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <span><i class="bi bi-people me-2 text-success"></i>Usuarios del Sistema</span>
    <span class="text-muted" style="font-size:.82rem">{{ $users->total() }} usuarios</span>
  </div>
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th class="px-4">Usuario</th><th>Rol</th><th>Cargo / Dependencia</th>
          <th class="text-center">Estado</th><th class="text-center">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $u)
        <tr>
          <td class="px-4">
            <div class="d-flex align-items-center gap-2">
              <div style="width:34px;height:34px;background:#e8f5ee;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <span class="fw-bold text-success" style="font-size:.78rem">{{ $u->initial }}</span>
              </div>
              <div>
                <div class="fw-semibold" style="font-size:.88rem">{{ $u->name }}</div>
                <div class="text-muted" style="font-size:.75rem">{{ $u->email }}</div>
              </div>
            </div>
          </td>
          <td>
            @php $rc=['admin'=>'danger','instructor'=>'primary','participant'=>'success'][$u->getRoleNames()->first()??'']??'secondary'; @endphp
            <span class="badge text-bg-{{ $rc }}" style="font-size:.7rem">{{ ucfirst($u->getRoleNames()->first()??'—') }}</span>
          </td>
          <td class="text-muted" style="font-size:.82rem">
            {{ $u->cargo ?? '—' }}
            @if($u->dependencia)<br><span style="font-size:.72rem">{{ $u->dependencia }}</span>@endif
          </td>
          <td class="text-center">
            <span class="badge" style="font-size:.7rem;background:{{ $u->active?'#d1fae5':'#fee2e2' }};color:{{ $u->active?'#065f46':'#991b1b' }}">
              {{ $u->active?'Activo':'Inactivo' }}
            </span>
          </td>
          <td class="text-center">
            <div class="d-flex justify-content-center gap-1">
              <a href="{{ route('users.show',$u) }}" class="btn btn-sm btn-outline-secondary" title="Ver">
                <i class="bi bi-eye"></i>
              </a>
              <a href="{{ route('users.edit',$u) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                <i class="bi bi-pencil"></i>
              </a>
              @if($u->id !== auth()->id())
              <form method="POST" action="{{ route('users.destroy',$u) }}" onsubmit="return confirm('¿Eliminar este usuario?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>
              </form>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="5" class="text-center py-5 text-muted"><i class="bi bi-people d-block mb-2 fs-1"></i>No se encontraron usuarios</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer d-flex justify-content-between align-items-center" style="font-size:.78rem">
    <span class="text-muted">Mostrando {{ $users->firstItem() }}–{{ $users->lastItem() }} de {{ $users->total() }}</span>
    {{ $users->links() }}
  </div>
</div>
@endsection
