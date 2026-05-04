@extends('layouts.app')
@section('title','Perfil de ' . $user->name)
@section('page-title','Perfil del Usuario')
@section('content')

<div class="row g-4">
  <div class="col-lg-4">
    <div class="card mb-3">
      <div style="height:70px;background:linear-gradient(135deg,#0d2d1a,#1a4d6e);border-radius:12px 12px 0 0"></div>
      <div class="card-body pt-0">
        <div style="margin-top:-28px;margin-bottom:.8rem">
          <div style="width:56px;height:56px;background:#e8f5ee;border:3px solid #fff;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,.12)">
            <span class="fw-bold text-success" style="font-size:1.3rem">{{ $user->initial }}</span>
          </div>
        </div>
        <h5 class="fw-bold mb-0">{{ $user->name }}</h5>
        <p class="text-muted mb-2" style="font-size:.82rem">{{ $user->email }}</p>
        @php $rc=['admin'=>'danger','instructor'=>'primary','participant'=>'success'][$user->getRoleNames()->first()??'']??'secondary'; @endphp
        <span class="badge text-bg-{{ $rc }} mb-1">{{ ucfirst($user->getRoleNames()->first()??'—') }}</span>
        <span class="badge ms-1" style="background:{{ $user->active?'#d1fae5':'#fee2e2' }};color:{{ $user->active?'#065f46':'#991b1b' }}">
          {{ $user->active?'Activo':'Inactivo' }}
        </span>
        <hr>
        <div class="d-flex flex-column gap-1" style="font-size:.83rem">
          @if($user->cargo)<div><i class="bi bi-briefcase me-2 text-muted"></i>{{ $user->cargo }}</div>@endif
          @if($user->dependencia)<div><i class="bi bi-building me-2 text-muted"></i>{{ $user->dependencia }}</div>@endif
          @if($user->telefono)<div><i class="bi bi-telephone me-2 text-muted"></i>{{ $user->telefono }}</div>@endif
          <div><i class="bi bi-calendar me-2 text-muted"></i>Desde {{ $user->created_at->format('M Y') }}</div>
        </div>
        <div class="d-flex gap-2 mt-3">
          <a href="{{ route('users.edit',$user) }}" class="btn btn-outline-primary btn-sm flex-grow-1">
            <i class="bi bi-pencil me-1"></i>Editar
          </a>
          @if($user->id !== auth()->id())
          <form method="POST" action="{{ route('users.destroy',$user) }}" onsubmit="return confirm('¿Eliminar este usuario?')">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
          </form>
          @endif
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="card mb-3">
      <div class="card-header"><i class="bi bi-journal-check me-2 text-success"></i>Cursos Inscritos ({{ $user->enrollments->count() }})</div>
      <div class="card-body p-0">
        @forelse($user->enrollments as $e)
        <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
          <i class="bi bi-book text-success"></i>
          <a href="{{ route('courses.show',$e->course) }}" class="flex-grow-1 text-decoration-none fw-semibold text-dark" style="font-size:.88rem">{{ $e->course->title }}</a>
          @php $ec=['active'=>'primary','completed'=>'success','dropped'=>'secondary'][$e->status]; @endphp
          <span class="badge text-bg-{{ $ec }}" style="font-size:.7rem">{{ ucfirst($e->status) }}</span>
        </div>
        @empty
        <div class="text-center py-4 text-muted" style="font-size:.85rem">Sin cursos inscritos.</div>
        @endforelse
      </div>
    </div>

    <div class="card mb-3">
      <div class="card-header"><i class="bi bi-award me-2 text-warning"></i>Certificados ({{ $user->certificates->count() }})</div>
      <div class="card-body p-0">
        @forelse($user->certificates as $cert)
        <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
          <i class="bi bi-award-fill text-warning fs-5 flex-shrink-0"></i>
          <div class="flex-grow-1">
            <div class="fw-semibold" style="font-size:.88rem">{{ $cert->course->title }}</div>
            <div style="font-family:monospace;font-size:.75rem;color:#64748b">{{ $cert->certificate_code }} · {{ $cert->issued_at->format('d/m/Y') }}</div>
          </div>
        </div>
        @empty
        <div class="text-center py-4 text-muted" style="font-size:.85rem">Sin certificados.</div>
        @endforelse
      </div>
    </div>

    <div class="card">
      <div class="card-header"><i class="bi bi-clipboard-data me-2 text-info"></i>Últimos Intentos de Evaluación</div>
      <div class="card-body p-0">
        @forelse($user->quizAttempts->take(10) as $a)
        <div class="d-flex align-items-center gap-3 px-4 py-2 border-bottom">
          <span class="badge {{ $a->passed?'text-bg-success':'text-bg-danger' }}" style="font-size:.72rem;flex-shrink:0">{{ $a->score }}%</span>
          <div class="flex-grow-1">
            <div style="font-size:.83rem;font-weight:600">{{ $a->quiz->title ?? '—' }}</div>
            <div class="text-muted" style="font-size:.72rem">{{ $a->created_at->format('d/m/Y H:i') }}</div>
          </div>
          <span class="badge {{ $a->passed?'text-bg-success':'text-bg-danger' }}" style="font-size:.7rem;flex-shrink:0">
            {{ $a->passed?'Aprobó':'No aprobó' }}
          </span>
        </div>
        @empty
        <div class="text-center py-4 text-muted" style="font-size:.85rem">Sin intentos.</div>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection
