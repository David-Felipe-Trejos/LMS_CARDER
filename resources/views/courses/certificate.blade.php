@extends('layouts.app')
@section('title','Certificado')@section('page-title','Certificado')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 no-print">
<a href="{{ route('courses.show',$course) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Volver al curso</a>
<button onclick="window.print()" class="btn btn-carder btn-sm"><i class="bi bi-printer me-1"></i>Imprimir / Guardar PDF</button>
</div>
<div class="row justify-content-center"><div class="col-lg-8">
<div id="certificate" style="border:4px solid #1e6b3a;border-radius:1.2rem;padding:3rem 3.5rem;background:linear-gradient(135deg,#fefffe 70%,#f0fdf4);text-align:center;position:relative;overflow:hidden">
<div style="position:absolute;top:-50px;right:-50px;width:220px;height:220px;border-radius:50%;background:rgba(30,107,58,.05)"></div>
<div style="position:absolute;bottom:-50px;left:-50px;width:180px;height:180px;border-radius:50%;background:rgba(30,107,58,.05)"></div>
<div style="position:absolute;top:0;left:0;right:0;height:6px;background:linear-gradient(90deg,#1e6b3a,#28a745,#1e6b3a)"></div>
<div style="display:inline-flex;align-items:center;gap:.8rem;margin-bottom:2rem">
<div style="width:52px;height:52px;background:#1e6b3a;border-radius:50%;display:flex;align-items:center;justify-content:center"><i class="bi bi-tree-fill text-white" style="font-size:1.4rem"></i></div>
<div class="text-start"><div style="font-family:'Nunito',sans-serif;font-weight:900;font-size:1.1rem;color:#1e6b3a">CARDER</div><div style="font-size:.68rem;color:#64748b;text-transform:uppercase;letter-spacing:1px">Corporación Autónoma Regional de Risaralda</div></div>
</div>
<div style="font-size:.8rem;color:#64748b;text-transform:uppercase;letter-spacing:2.5px;margin-bottom:.6rem">Certifica que</div>
<h2 style="font-family:'Nunito',sans-serif;font-weight:900;font-size:2.2rem;color:#0d2d1a;margin-bottom:.3rem">{{ $user->name }}</h2>
@if($user->cargo || $user->dependencia)<div style="font-size:.88rem;color:#64748b;margin-bottom:1.5rem">{{ $user->cargo }}{{ $user->cargo && $user->dependencia?' — ':'' }}{{ $user->dependencia }}</div>@else<div style="margin-bottom:1.5rem"></div>@endif
<div style="font-size:.9rem;color:#475569;margin-bottom:.5rem">Ha completado satisfactoriamente el curso</div>
<h3 style="font-family:'Nunito',sans-serif;font-weight:800;font-size:1.5rem;color:#1e6b3a;margin-bottom:.4rem">{{ $course->title }}</h3>
<div style="font-size:.82rem;color:#64748b;margin-bottom:2.5rem">Duración: {{ $course->duration_hours }} horas · Modalidad: Virtual · {{ $course->category_label }}</div>
<div class="row justify-content-center mb-3"><div class="col-5"><div style="border-top:2px solid #1e6b3a;padding-top:.6rem"><div style="font-weight:700;font-size:.88rem;color:#1e6b3a">{{ $cert->issued_at->format('d \\d\\e F \\d\\e Y') }}</div><div style="font-size:.72rem;color:#94a3b8">Fecha de emisión</div></div></div><div class="col-5"><div style="border-top:2px solid #1e6b3a;padding-top:.6rem"><div style="font-weight:700;font-size:.88rem;color:#1e6b3a">Director(a) CARDER</div><div style="font-size:.72rem;color:#94a3b8">Firma autorizada</div></div></div></div>
<div style="display:inline-block;background:#f0fdf4;border:1.5px solid #bbf7d0;border-radius:.6rem;padding:.5rem 1.5rem"><div style="font-size:.65rem;color:#166534;text-transform:uppercase;letter-spacing:1.2px">Código de verificación</div><div style="font-family:'Courier New',monospace;font-size:1rem;font-weight:700;color:#1e6b3a;letter-spacing:2px">{{ $cert->certificate_code }}</div></div>
</div>
<p class="text-center text-muted mt-3 no-print" style="font-size:.78rem"><i class="bi bi-info-circle me-1"></i>Para guardar como PDF: usa el botón "Imprimir" y selecciona "Guardar como PDF".</p>
</div></div>
@push('styles')
<style>
@media print{.no-print,#sidebar,#topbar{display:none!important}#main{margin-left:0!important}body{background:#fff!important}}
</style>
@endpush
@endsection
