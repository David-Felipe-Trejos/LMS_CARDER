@extends('layouts.app')
@section('title','Reportes')
@section('page-title','Reportes Institucionales')
@section('content')

{{-- STATS --}}
<div class="row g-3 mb-4">
  @php $cards=[
    ['Total Usuarios',   $stats['total_users'],       'bi-people-fill',       'linear-gradient(135deg,#1e6b3a,#28a745)'],
    ['Total Cursos',     $stats['total_courses'],      'bi-journal-bookmark',  'linear-gradient(135deg,#1a4d6e,#0d6efd)'],
    ['Matrículas',       $stats['total_enrollments'],  'bi-person-check-fill', 'linear-gradient(135deg,#7c3d00,#fd7e14)'],
    ['Certificados',     $stats['total_certs'],        'bi-award-fill',        'linear-gradient(135deg,#4a0072,#a855f7)'],
  ]; @endphp
  @foreach($cards as [$label,$val,$icon,$bg])
  <div class="col-6 col-md-3">
    <div class="stat-card" style="background:{{ $bg }}">
      <div class="stat-number">{{ $val }}</div>
      <div class="stat-label">{{ $label }}</div>
      <i class="bi {{ $icon }} stat-icon"></i>
    </div>
  </div>
  @endforeach
</div>

<div class="row g-4 mb-4">
  {{-- Gráfico --}}
  <div class="col-lg-7">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-graph-up me-2 text-success"></i>Matrículas {{ date('Y') }}</span>
        <div class="d-flex gap-2">
          <a href="{{ route('reports.pdf') }}" class="btn btn-sm btn-outline-danger" target="_blank">
            <i class="bi bi-file-pdf me-1"></i>PDF
          </a>
        </div>
      </div>
      <div class="card-body"><canvas id="chartM" height="220"></canvas></div>
    </div>
  </div>
  {{-- Dona aprobación --}}
  <div class="col-lg-5">
    <div class="card h-100">
      <div class="card-header"><i class="bi bi-pie-chart me-2 text-primary"></i>Tasa de Aprobación</div>
      <div class="card-body d-flex flex-column align-items-center justify-content-center">
        <canvas id="chartP" height="180" style="max-width:180px"></canvas>
        <div class="text-center mt-3">
          <div class="fw-bold text-success" style="font-size:1.6rem;font-family:'Nunito',sans-serif">{{ $stats['pass_rate'] }}%</div>
          <div class="text-muted" style="font-size:.78rem">de evaluaciones aprobadas</div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- TABLA DE CURSOS --}}
<div class="card">
  <div class="card-header"><i class="bi bi-table me-2 text-success"></i>Reporte Detallado por Curso</div>
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th class="px-4">Curso</th>
          <th>Instructor</th>
          <th class="text-center">Matriculados</th>
          <th class="text-center">Completados</th>
          <th class="text-center">Certificados</th>
          <th style="min-width:160px">Completitud</th>
          <th class="text-center">Estado</th>
        </tr>
      </thead>
      <tbody>
        @foreach($courses as $c)
        <tr>
          <td class="px-4">
            <a href="{{ route('courses.show',$c) }}" class="fw-semibold text-decoration-none text-dark" style="font-size:.87rem">
              {{ Str::limit($c->title,42) }}
            </a>
            <div style="font-size:.72rem;color:{{ $c->category_color }};font-weight:600">{{ $c->category_label }}</div>
          </td>
          <td class="text-muted" style="font-size:.83rem">{{ $c->instructor->name }}</td>
          <td class="text-center fw-bold">{{ $c->enrollments_count }}</td>
          <td class="text-center">{{ $c->completed_count }}</td>
          <td class="text-center">{{ $c->certificates->count() }}</td>
          <td>
            <div class="d-flex align-items-center gap-2">
              <div class="progress flex-grow-1" style="height:6px">
                <div class="progress-bar" style="width:{{ $c->completion_rate }}%;background:{{ $c->completion_rate>=70?'#1e6b3a':($c->completion_rate>=40?'#fd7e14':'#dc3545') }}"></div>
              </div>
              <span style="font-size:.75rem;font-weight:700;min-width:32px;color:#475569">{{ $c->completion_rate }}%</span>
            </div>
          </td>
          <td class="text-center">
            @php $sc=['draft'=>'secondary','published'=>'success','archived'=>'warning'][$c->status]; @endphp
            <span class="badge text-bg-{{ $sc }}" style="font-size:.7rem">{{ ucfirst($c->status) }}</span>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@push('scripts')
<script>
const meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
const rawM  = @json($monthlyEnrollments);
new Chart(document.getElementById('chartM'),{
  type:'bar',
  data:{labels:rawM.map(d=>meses[d.month-1]),datasets:[{
    label:'Matrículas',data:rawM.map(d=>d.total),
    backgroundColor:'rgba(30,107,58,.75)',borderRadius:6,borderSkipped:false,
  }]},
  options:{responsive:true,plugins:{legend:{display:false}},
    scales:{y:{beginAtZero:true,grid:{color:'#f1f5f9'}},x:{grid:{display:false}}}}
});
const pr = {{ $stats['pass_rate'] }};
new Chart(document.getElementById('chartP'),{
  type:'doughnut',
  data:{datasets:[{data:[pr,100-pr],backgroundColor:['#1e6b3a','#dc3545'],borderWidth:0,hoverOffset:4}]},
  options:{cutout:'65%',plugins:{legend:{display:false}},responsive:true}
});
</script>
@endpush
@endsection
