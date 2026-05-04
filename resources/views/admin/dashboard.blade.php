@extends('layouts.app')
@section('title','Dashboard Admin')@section('page-title','Dashboard Administrativo')
@section('content')
<div class="row g-3 mb-4">
@php $cards=[['Usuarios','totalUsers','bi-people-fill','linear-gradient(135deg,#1e6b3a,#28a745)'],['Cursos','totalCourses','bi-journal-bookmark-fill','linear-gradient(135deg,#1a4d6e,#0d6efd)'],['Matrículas','totalEnrollments','bi-person-check-fill','linear-gradient(135deg,#7c3d00,#fd7e14)'],['Certificados','totalCerts','bi-award-fill','linear-gradient(135deg,#4a0072,#a855f7)']]; @endphp
@foreach($cards as [$label,$key,$icon,$bg])
<div class="col-6 col-md-3"><div class="stat-card" style="background:{{ $bg }}"><div class="stat-number">{{ $$key }}</div><div class="stat-label">{{ $label }}</div><i class="bi {{ $icon }} stat-icon"></i></div></div>
@endforeach
</div>
<div class="row g-4 mb-4">
<div class="col-lg-7"><div class="card h-100"><div class="card-header d-flex justify-content-between align-items-center"><span><i class="bi bi-graph-up-arrow me-2 text-success"></i>Matrículas {{ date('Y') }}</span><a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-success">Ver Reportes</a></div><div class="card-body"><canvas id="chartE" height="220"></canvas></div></div></div>
<div class="col-lg-5"><div class="card h-100"><div class="card-header d-flex justify-content-between align-items-center"><span><i class="bi bi-collection me-2 text-primary"></i>Cursos Recientes</span><a href="{{ route('courses.create') }}" class="btn btn-sm btn-carder"><i class="bi bi-plus me-1"></i>Nuevo</a></div><div class="card-body p-0">
@foreach($courseStats as $c)
<a href="{{ route('courses.show',$c) }}" class="d-flex align-items-center gap-3 px-3 py-2 border-bottom text-decoration-none">
<div style="width:38px;height:38px;border-radius:8px;background:{{ $c->category_color }};display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="bi bi-book text-white"></i></div>
<div class="flex-grow-1 overflow-hidden"><div class="fw-semibold text-dark text-truncate" style="font-size:.85rem">{{ $c->title }}</div><div class="text-muted" style="font-size:.75rem">{{ $c->enrollments_count }} participantes</div></div>
@php $sc=['draft'=>'secondary','published'=>'success','archived'=>'warning'][$c->status]; @endphp
<span class="badge text-bg-{{ $sc }}" style="font-size:.68rem;flex-shrink:0">{{ ucfirst($c->status) }}</span></a>
@endforeach
</div></div></div>
</div>
<div class="card"><div class="card-header d-flex justify-content-between align-items-center"><span><i class="bi bi-clock-history me-2 text-info"></i>Últimas Inscripciones</span><a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary">Ver Usuarios</a></div>
<div class="table-responsive"><table class="table align-middle mb-0"><thead class="table-light"><tr><th class="px-3">Participante</th><th>Curso</th><th>Dependencia</th><th class="text-center">Estado</th><th>Fecha</th></tr></thead><tbody>
@foreach($recentEnrollments as $e)
<tr><td class="px-3"><div class="d-flex align-items-center gap-2"><div style="width:30px;height:30px;background:#e8f5ee;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0"><span class="fw-bold text-success" style="font-size:.72rem">{{ $e->user->initial }}</span></div><div><div class="fw-semibold" style="font-size:.85rem">{{ $e->user->name }}</div><div class="text-muted" style="font-size:.75rem">{{ $e->user->email }}</div></div></div></td>
<td><a href="{{ route('courses.show',$e->course) }}" class="text-decoration-none fw-semibold" style="font-size:.85rem">{{ Str::limit($e->course->title,40) }}</a></td>
<td class="text-muted" style="font-size:.82rem">{{ $e->user->dependencia ?? '—' }}</td>
<td class="text-center">@php $ec=['active'=>'primary','completed'=>'success','dropped'=>'secondary'][$e->status]; @endphp<span class="badge text-bg-{{ $ec }}" style="font-size:.7rem">{{ ucfirst($e->status) }}</span></td>
<td class="text-muted" style="font-size:.8rem">{{ $e->enrolled_at->format('d/m/Y') }}</td></tr>
@endforeach
</tbody></table></div></div>
@push('scripts')
<script>
const meses=['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
const raw=@json($monthlyData);
new Chart(document.getElementById('chartE'),{type:'bar',data:{labels:raw.map(d=>meses[d.month-1]),datasets:[{label:'Matrículas',data:raw.map(d=>d.total),backgroundColor:'rgba(30,107,58,.75)',borderRadius:6,borderSkipped:false}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,grid:{color:'#f1f5f9'}},x:{grid:{display:false}}}}});
</script>
@endpush
@endsection
