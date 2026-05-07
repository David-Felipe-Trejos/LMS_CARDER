<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reporte LMS CARDER {{ date('Y') }}</title>
  <style>
    body { font-family: Arial, sans-serif; font-size: 11px; color: #333; margin: 20px; }
    h1 { color: #1e6b3a; font-size: 18px; border-bottom: 2px solid #1e6b3a; padding-bottom: 6px; margin-bottom: 16px; }
    h2 { color: #1a2332; font-size: 13px; margin: 18px 0 8px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    th { background: #1e6b3a; color: #fff; padding: 6px 8px; text-align: left; font-size: 10px; }
    td { padding: 5px 8px; border-bottom: 1px solid #e5e7eb; font-size: 10px; }
    tr:nth-child(even) td { background: #f8f9fa; }
    .badge-pub   { background: #d1fae5; color: #065f46; border-radius: 4px; padding: 2px 6px; font-size: 9px; }
    .badge-draft { background: #f0f0f0; color: #555;    border-radius: 4px; padding: 2px 6px; font-size: 9px; }
    .badge-arch  { background: #fef3c7; color: #92400e; border-radius: 4px; padding: 2px 6px; font-size: 9px; }
    .footer { text-align: center; margin-top: 30px; color: #aaa; font-size: 9px; border-top: 1px solid #eee; padding-top: 10px; }
    .stats-row { display: table; width: 100%; }
    .stat-box { display: table-cell; padding: 10px; text-align: center; border: 1px solid #e5e7eb; background: #f8fafc; }
    .stat-num { font-size: 18px; font-weight: bold; color: #1e6b3a; }
    .stat-lbl { font-size: 9px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
    @media print { body { margin: 0; } .no-print { display: none; } }
  </style>
</head>
<body>

<div class="no-print" style="margin-bottom: 16px">
  <button onclick="window.print()" style="background:#1e6b3a;color:#fff;border:none;padding:8px 20px;border-radius:6px;cursor:pointer;font-size:13px">
    🖨️ Imprimir / Guardar PDF
  </button>
  <a href="{{ route('reports.index') }}" style="margin-left:10px;font-size:12px;color:#1e6b3a">← Volver a Reportes</a>
</div>

<h1>🌿 LMS CARDER — Reporte Institucional {{ date('Y') }}</h1>
<p style="color:#64748b;font-size:10px;margin-bottom:16px">
  Generado el {{ now()->format('d/m/Y H:i') }} · Sistema de Gestión del Aprendizaje CARDER
</p>

<h2>📊 Resumen General</h2>
<div class="stats-row">
  <div class="stat-box">
    <div class="stat-num">{{ $stats['total_users'] ?? 0 }}</div>
    <div class="stat-lbl">Usuarios</div>
  </div>
  <div class="stat-box">
    <div class="stat-num">{{ $stats['total_courses'] ?? 0 }}</div>
    <div class="stat-lbl">Cursos</div>
  </div>
  <div class="stat-box">
    <div class="stat-num">{{ $stats['total_enrollments'] ?? 0 }}</div>
    <div class="stat-lbl">Matrículas</div>
  </div>
  <div class="stat-box">
    <div class="stat-num">{{ $stats['total_certs'] ?? 0 }}</div>
    <div class="stat-lbl">Certificados</div>
  </div>
  <div class="stat-box">
    <div class="stat-num">{{ $stats['pass_rate'] ?? 0 }}%</div>
    <div class="stat-lbl">Tasa Aprobación</div>
  </div>
</div>

<h2>📚 Detalle por Curso</h2>
<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Curso</th>
      <th>Instructor</th>
      <th>Estado</th>
      <th style="text-align:center">Matriculados</th>
      <th style="text-align:center">Completados</th>
      <th style="text-align:center">% Completitud</th>
    </tr>
  </thead>
  <tbody>
    @forelse($courses as $i => $c)
      <tr>
        <td>{{ $i + 1 }}</td>
        <td><strong>{{ $c->title }}</strong></td>
        <td>{{ $c->instructor->name ?? '—' }}</td>
        <td>
          @php
            $cls = ['published' => 'badge-pub', 'draft' => 'badge-draft', 'archived' => 'badge-arch'][$c->status] ?? 'badge-draft';
          @endphp
          <span class="{{ $cls }}">{{ ucfirst($c->status) }}</span>
        </td>
        <td style="text-align:center">{{ $c->enrollments_count ?? 0 }}</td>
        <td style="text-align:center">{{ $c->completed_count ?? 0 }}</td>
        <td style="text-align:center"><strong>{{ $c->completion_rate ?? 0 }}%</strong></td>
      </tr>
    @empty
      <tr><td colspan="7" style="text-align:center;color:#999">Sin cursos registrados</td></tr>
    @endforelse
  </tbody>
</table>

<div class="footer">
  LMS CARDER · Corporación Autónoma Regional de Risaralda · www.carder.gov.co · Pereira, Risaralda
</div>
</body>
</html>