@extends('layouts.app')
@section('title','Mis Reportes')
@section('page-title','Mis Reportes')
@section('content')

<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="stat-card" style="background:linear-gradient(135deg,#1e6b3a,#28a745)">
      <div class="stat-number">{{ $courses->count() }}</div><div class="stat-label">Mis Cursos</div>
      <i class="bi bi-mortarboard-fill stat-icon"></i>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card" style="background:linear-gradient(135deg,#1a4d6e,#0d6efd)">
      <div class="stat-number">{{ $courses->sum('enrollments_count') }}</div><div class="stat-label">Participantes</div>
      <i class="bi bi-people-fill stat-icon"></i>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card" style="background:linear-gradient(135deg,#7c3d00,#fd7e14)">
      <div class="stat-number">{{ $attempts->where('passed',true)->count() }}</div><div class="stat-label">Aprobaron</div>
      <i class="bi bi-check-circle-fill stat-icon"></i>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card" style="background:linear-gradient(135deg,#4a0072,#a855f7)">
      <div class="stat-number">{{ $courses->sum('completed_count') }}</div><div class="stat-label">Completaron</div>
      <i class="bi bi-award-fill stat-icon"></i>
    </div>
  </div>
</div>

<div class="card mb-4">
  <div class="card-header"><i class="bi bi-table me-2 text-success"></i>Mis Cursos</div>
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead class="table-light">
        <tr><th class="px-4">Curso</th><th class="text-center">Matriculados</th><th class="text-center">Completados</th><th>Completitud</th><th class="text-center">Acciones</th></tr>
      </thead>
      <tbody>
        @foreach($courses as $c)
        <tr>
          <td class="px-4 fw-semibold" style="font-size:.87rem">{{ $c->title }}</td>
          <td class="text-center">{{ $c->enrollments_count }}</td>
          <td class="text-center">{{ $c->completed_count }}</td>
          <td>
            <div class="d-flex align-items-center gap-2">
              <div class="progress flex-grow-1" style="height:6px">
                <div class="progress-bar bg-success" style="width:{{ $c->completion_rate }}%"></div>
              </div>
              <span style="font-size:.75rem;font-weight:700">{{ $c->completion_rate }}%</span>
            </div>
          </td>
          <td class="text-center">
            <a href="{{ route('courses.show',$c) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<div class="card">
  <div class="card-header"><i class="bi bi-clipboard-data me-2 text-info"></i>Intentos de Evaluación Recientes</div>
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead class="table-light">
        <tr><th class="px-4">Participante</th><th>Evaluación</th><th class="text-center">Puntaje</th><th class="text-center">Resultado</th><th>Fecha</th></tr>
      </thead>
      <tbody>
        @forelse($attempts as $a)
        <tr>
          <td class="px-4" style="font-size:.85rem">{{ $a->user->name ?? '—' }}</td>
          <td style="font-size:.83rem">{{ $a->quiz->title ?? '—' }}</td>
          <td class="text-center fw-bold {{ $a->passed?'text-success':'text-danger' }}">{{ $a->score }}%</td>
          <td class="text-center">
            <span class="badge {{ $a->passed?'text-bg-success':'text-bg-danger' }}" style="font-size:.7rem">
              {{ $a->passed?'Aprobado':'No aprobado' }}
            </span>
          </td>
          <td class="text-muted" style="font-size:.8rem">{{ $a->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        @empty
        <tr><td colspan="5" class="text-center py-4 text-muted">Sin intentos registrados.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
