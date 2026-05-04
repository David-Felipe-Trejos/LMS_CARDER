@extends('layouts.app')
@section('title', $lesson->title)
@section('page-title', 'Lección')
@section('content')

<div class="row g-4">

  {{-- CONTENIDO PRINCIPAL --}}
  <div class="col-lg-8">

    {{-- Breadcrumb --}}
    <nav style="font-size:.82rem" class="mb-3">
      <a href="{{ route('courses.show',$course) }}" class="text-success text-decoration-none fw-semibold">
        <i class="bi bi-arrow-left me-1"></i>{{ Str::limit($course->title,35) }}
      </a>
      <span class="text-muted mx-2">/</span>
      <span class="text-muted">{{ $lesson->module->title }}</span>
      <span class="text-muted mx-2">/</span>
      <span class="text-dark">{{ $lesson->title }}</span>
    </nav>

    {{-- Card de lección --}}
    <div class="card mb-4">
      <div class="card-header d-flex align-items-center gap-2">
        @php $ico=['text'=>'bi-file-text-fill text-primary','video'=>'bi-play-circle-fill text-danger','pdf'=>'bi-file-pdf-fill text-danger','link'=>'bi-link-45deg text-info'][$lesson->type]??'bi-file-fill'; @endphp
        <i class="bi {{ $ico }} fs-5"></i>
        <span class="fw-bold" style="font-size:.95rem">{{ $lesson->title }}</span>
        @if($lesson->duration_minutes)
        <span class="badge bg-light text-dark border ms-auto" style="font-size:.72rem">
          <i class="bi bi-clock me-1"></i>{{ $lesson->duration_minutes }} min
        </span>
        @endif
      </div>
      <div class="card-body p-4">

        {{-- VIDEO --}}
        @if($lesson->type === 'video')
        <div style="position:relative;padding-bottom:56.25%;height:0;border-radius:.6rem;overflow:hidden;background:#000">
          <iframe src="{{ $lesson->content }}" style="position:absolute;top:0;left:0;width:100%;height:100%"
                  frameborder="0" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture">
          </iframe>
        </div>

        {{-- PDF --}}
        @elseif($lesson->type === 'pdf')
        <div class="d-flex align-items-center gap-4 p-4 border rounded-3 bg-light mb-3">
          <i class="bi bi-file-pdf-fill text-danger" style="font-size:3rem;flex-shrink:0"></i>
          <div>
            <div class="fw-semibold mb-1">Documento PDF</div>
            <p class="text-muted mb-2" style="font-size:.83rem">Haz clic para abrir o descargar el documento.</p>
            <a href="{{ $lesson->content }}" target="_blank" class="btn btn-outline-danger btn-sm">
              <i class="bi bi-download me-1"></i>Abrir PDF
            </a>
          </div>
        </div>

        {{-- ENLACE --}}
        @elseif($lesson->type === 'link')
        <div class="d-flex align-items-center gap-4 p-4 border rounded-3 bg-light">
          <i class="bi bi-link-45deg text-info" style="font-size:3rem;flex-shrink:0"></i>
          <div>
            <div class="fw-semibold mb-1">Recurso externo</div>
            <p class="text-muted mb-2" style="font-size:.83rem">{{ $lesson->content }}</p>
            <a href="{{ $lesson->content }}" target="_blank" rel="noopener" class="btn btn-outline-info btn-sm">
              <i class="bi bi-box-arrow-up-right me-1"></i>Abrir enlace
            </a>
          </div>
        </div>

        {{-- TEXTO --}}
        @else
        <div style="font-size:.93rem;line-height:1.75;color:#1e293b">
          {!! $lesson->content !!}
        </div>
        @endif

      </div>
    </div>

    {{-- NAVEGACIÓN ANTERIOR / SIGUIENTE --}}
    <div class="d-flex justify-content-between gap-3">
      @if($prev)
      <a href="{{ route('lessons.show',[$course,$prev]) }}" class="btn btn-outline-secondary">
        <i class="bi bi-chevron-left me-1"></i>{{ Str::limit($prev->title,28) }}
      </a>
      @else
      <div></div>
      @endif

      @if($next)
      <a href="{{ route('lessons.show',[$course,$next]) }}" class="btn btn-carder">
        {{ Str::limit($next->title,28) }}<i class="bi bi-chevron-right ms-1"></i>
      </a>
      @else
      <a href="{{ route('courses.show',$course) }}" class="btn btn-success">
        <i class="bi bi-check2-circle me-1"></i>Finalizar módulo
      </a>
      @endif
    </div>

  </div>

  {{-- SIDEBAR --}}
  <div class="col-lg-4">

    {{-- Progreso --}}
    <div class="card mb-3">
      <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
          <span style="font-size:.78rem;font-weight:600;color:#64748b">Tu progreso en el curso</span>
          <span class="fw-bold text-success" style="font-size:.78rem" id="progressPct">{{ $progress }}%</span>
        </div>
        <div class="progress"><div class="progress-bar" id="progressBar" style="width:{{ $progress }}%"></div></div>
      </div>
    </div>

    {{-- Módulos del curso --}}
    <div class="card">
      <div class="card-header" style="font-size:.84rem;font-weight:600">
        <i class="bi bi-list-ul me-2 text-success"></i>Contenido
      </div>
      <div class="accordion" id="sideAcc">
        @foreach($course->modules as $mi => $module)
        <div class="accordion-item border-0 border-bottom">
          <h2 class="accordion-header">
            <button class="accordion-button {{ $module->id !== $lesson->module_id ? 'collapsed':'' }} py-2 px-3 fw-semibold"
                    type="button" data-bs-toggle="collapse" data-bs-target="#sm{{ $module->id }}"
                    style="font-size:.82rem;background:#fafafa">
              <i class="bi bi-folder2 me-2 text-warning"></i>{{ $module->title }}
            </button>
          </h2>
          <div id="sm{{ $module->id }}" class="accordion-collapse collapse {{ $module->id===$lesson->module_id?'show':'' }}">
            <div class="accordion-body p-0">
              @foreach($module->lessons as $l)
              @php $isActive = $l->id===$lesson->id; $isDone = $l->isCompletedByUser(auth()->id()); @endphp
              <a href="{{ route('lessons.show',[$course,$l]) }}"
                 class="d-flex align-items-center gap-2 px-3 py-2 border-top text-decoration-none {{ $isActive?'bg-success bg-opacity-10':'' }}"
                 style="font-size:.8rem;color:{{ $isActive?'#1e6b3a':'#475569' }}">
                <i class="bi {{ $isDone?'bi-check-circle-fill text-success':($isActive?'bi-play-circle-fill text-success':'bi-circle text-muted') }}"
                   style="flex-shrink:0;font-size:.85rem"></i>
                <span class="text-truncate {{ $isActive?'fw-semibold':'' }}">{{ $l->title }}</span>
              </a>
              @endforeach
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>

  </div>
</div>

@push('scripts')
<script>
// Marcar lección como completada via AJAX
fetch("{{ route('lessons.complete',[$course,$lesson]) }}", {
  method: 'POST',
  headers: {
    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
})
.then(r => r.json())
.then(data => {
  if (data.success) {
    document.getElementById('progressPct').textContent = data.progress + '%';
    document.getElementById('progressBar').style.width  = data.progress + '%';
  }
})
.catch(err => console.log('Progress update:', err));
</script>
@endpush
@endsection
