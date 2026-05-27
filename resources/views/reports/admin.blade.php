@extends('layouts.app')
@section('title','Reportes')
@section('page-title','Panel de Reportes')
 
@section('content')
 
{{-- Encabezado con acciones --}}
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
  <div>
    <h4 class="fw-bold mb-1" style="color:var(--green-dark)">
      <i class="bi bi-bar-chart-fill me-2"></i>Reportes Institucionales
    </h4>
    <p class="text-muted mb-0" style="font-size:.85rem">
      Resumen general del sistema, desempeño por curso y estadísticas de matrícula.
    </p>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('reports.pdf') }}" target="_blank" class="btn btn-outline-success">
      <i class="bi bi-file-earmark-pdf me-1"></i>Exportar PDF
    </a>
    <button onclick="exportCSV()" class="btn btn-carder">
      <i class="bi bi-file-earmark-spreadsheet me-1"></i>Exportar CSV
    </button>
  </div>
</div>
 
{{-- Tarjetas de estadísticas --}}
<div class="row g-3 mb-4">
  @php
    $statCards = [
      ['Usuarios',     'total_users',       'bi-people-fill',       'linear-gradient(135deg,#1e6b3a,#28a745)'],
      ['Cursos',       'total_courses',     'bi-journal-bookmark-fill','linear-gradient(135deg,#1a4d6e,#0d6efd)'],
      ['Matrículas',   'total_enrollments', 'bi-person-check-fill', 'linear-gradient(135deg,#7c3d00,#fd7e14)'],
      ['Certificados', 'total_certs',       'bi-award-fill',        'linear-gradient(135deg,#4a0072,#a855f7)'],
      ['Tasa Aprob.',  'pass_rate',         'bi-check2-circle',     'linear-gradient(135deg,#0f4c5c,#17a2b8)'],
    ];
  @endphp
  @foreach($statCards as [$label, $key, $icon, $bg])
    <div class="col-6 col-md-4 col-lg">
      <div class="stat-card" style="background:{{ $bg }}">
        <div class="stat-number">
          {{ $stats[$key] ?? 0 }}@if($key === 'pass_rate')%@endif
        </div>
        <div class="stat-label">{{ $label }}</div>
        <i class="bi {{ $icon }} stat-icon"></i>
      </div>
    </div>
  @endforeach
</div>
 
{{-- Gráfica de matrículas mensuales --}}
<div class="card mb-4">
  <div class="card-header d-flex justify-content-between align-items-center">
    <span><i class="bi bi-graph-up-arrow me-2 text-success"></i>Matrículas por mes — {{ date('Y') }}</span>
    <span class="badge text-bg-light" style="font-size:.72rem">
      Total {{ $monthlyEnrollments->sum('total') }}
    </span>
  </div>
  <div class="card-body">
    <canvas id="chartMonthly" height="90"></canvas>
  </div>
</div>
 
{{-- Tabla detallada de cursos --}}
<div class="card mb-4">
  <div class="card-header d-flex justify-content-between align-items-center">
    <span><i class="bi bi-table me-2 text-primary"></i>Desempeño por Curso</span>
    <input type="text" id="filterCourse" class="form-control form-control-sm"
           placeholder="Filtrar por nombre..." style="width:220px;font-size:.82rem">
  </div>
  <div class="table-responsive">
    <table class="table align-middle mb-0" id="tblCourses">
      <thead class="table-light">
        <tr>
          <th class="px-3">#</th>
          <th>Curso</th>
          <th>Instructor</th>
          <th class="text-center">Estado</th>
          <th class="text-center">Matriculados</th>
          <th class="text-center">Completados</th>
          <th class="text-center">Certificados</th>
          <th style="min-width:160px">% Completitud</th>
          <th class="text-center">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($courses as $i => $c)
          @php
            $statusClass = [
              'draft'     => 'secondary',
              'published' => 'success',
              'archived'  => 'warning',
            ][$c->status] ?? 'secondary';
          @endphp
          <tr>
            <td class="px-3 text-muted">{{ $i + 1 }}</td>
            <td>
              <div class="fw-semibold text-dark" style="font-size:.87rem">{{ $c->title }}</div>
              <div class="text-muted" style="font-size:.72rem">
                {{ $c->category_label ?? 'Sin categoría' }}
              </div>
            </td>
            <td style="font-size:.82rem">{{ $c->instructor->name ?? '—' }}</td>
            <td class="text-center">
              <span class="badge text-bg-{{ $statusClass }}" style="font-size:.7rem">
                {{ ucfirst($c->status) }}
              </span>
            </td>
            <td class="text-center fw-semibold">{{ $c->enrollments_count }}</td>
            <td class="text-center">{{ $c->completed_count }}</td>
            <td class="text-center">{{ $c->certificates->count() }}</td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <div class="progress flex-grow-1" style="height:6px">
                  <div class="progress-bar bg-success" style="width:{{ $c->completion_rate }}%"></div>
                </div>
                <span style="font-size:.75rem;font-weight:700;min-width:35px;text-align:right">
                  {{ $c->completion_rate }}%
                </span>
              </div>
            </td>
            <td class="text-center">
              <a href="{{ route('courses.show', $c) }}" class="btn btn-sm btn-outline-secondary"
                 title="Ver curso">
                <i class="bi bi-eye"></i>
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="9" class="text-center py-4 text-muted">
              <i class="bi bi-inbox fs-3 d-block mb-2"></i>
              No hay cursos registrados todavía.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
 
@push('scripts')
<script>
  // === Gráfica de matrículas mensuales ===
  const meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
  const rawMonthly = @json($monthlyEnrollments);
 
  // Rellenar los 12 meses, incluso los vacíos
  const dataByMonth = Array(12).fill(0);
  rawMonthly.forEach(d => { dataByMonth[d.month - 1] = d.total; });
 
  new Chart(document.getElementById('chartMonthly'), {
    type: 'bar',
    data: {
      labels: meses,
      datasets: [{
        label: 'Matrículas',
        data: dataByMonth,
        backgroundColor: 'rgba(30,107,58,.75)',
        borderRadius: 6,
        borderSkipped: false
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f1f5f9' } },
        x: { grid: { display: false } }
      }
    }
  });
 
  // === Filtro en vivo de la tabla de cursos ===
  document.getElementById('filterCourse').addEventListener('input', function (e) {
    const q = e.target.value.toLowerCase().trim();
    document.querySelectorAll('#tblCourses tbody tr').forEach(tr => {
      tr.style.display = tr.innerText.toLowerCase().includes(q) ? '' : 'none';
    });
  });
 
  // === Exportar tabla de cursos a CSV ===
  function exportCSV() {
    const rows = [['#','Curso','Instructor','Estado','Matriculados','Completados','Certificados','% Completitud']];
    @foreach($courses as $i => $c)
      rows.push([
        {{ $i + 1 }},
        @json($c->title),
        @json($c->instructor->name ?? '—'),
        @json(ucfirst($c->status)),
        {{ $c->enrollments_count }},
        {{ $c->completed_count }},
        {{ $c->certificates->count() }},
        '{{ $c->completion_rate }}%'
      ]);
    @endforeach
 
    const csv = rows.map(r => r.map(cell => {
      const s = String(cell ?? '');
      return /[",\n;]/.test(s) ? `"${s.replace(/"/g, '""')}"` : s;
    }).join(';')).join('\n');
 
    const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href = url;
    a.download = 'reporte_cursos_{{ date("Y-m-d") }}.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
  }
</script>
@endpush
@endsection