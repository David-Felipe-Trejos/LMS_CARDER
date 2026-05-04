@extends('layouts.app')
@section('title',$course->title)
@section('page-title','Detalle del Curso')
@section('content')

<div class="row g-4">
  {{-- COLUMNA PRINCIPAL --}}
  <div class="col-lg-8">

    {{-- CABECERA --}}
    <div style="background:linear-gradient(135deg,{{ $course->category_color }},{{ $course->category_color }}99);border-radius:14px;padding:2rem;color:#fff;margin-bottom:1.5rem">
      <span style="background:rgba(255,255,255,.2);border-radius:20px;padding:.2rem .8rem;font-size:.72rem;font-weight:700;text-transform:uppercase">
        {{ $course->category_label }}
      </span>
      <h2 class="fw-bold mt-2 mb-1" style="font-size:1.4rem">{{ $course->title }}</h2>
      <p style="opacity:.85;font-size:.88rem;margin-bottom:1rem">{{ $course->description }}</p>
      <div class="d-flex flex-wrap gap-3" style="font-size:.82rem;opacity:.9">
        <span><i class="bi bi-person me-1"></i>{{ $course->instructor->name }}</span>
        <span><i class="bi bi-clock me-1"></i>{{ $course->duration_hours }}h</span>
        <span><i class="bi bi-people me-1"></i>{{ $course->enrollments->count() }} participantes</span>
        <span><i class="bi bi-folder2 me-1"></i>{{ $course->modules->count() }} módulos</span>
      </div>
    </div>

    {{-- MÓDULOS Y LECCIONES --}}
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-list-ul me-2 text-success"></i>Contenido del Curso</span>
        @can('update',$course)
        <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalAddModule">
          <i class="bi bi-folder-plus me-1"></i>Agregar Módulo
        </button>
        @endcan
      </div>
      <div class="accordion" id="accordionModules">
        @forelse($course->modules as $mi => $module)
        <div class="accordion-item border-0 border-bottom">
          <h2 class="accordion-header">
            <button class="accordion-button {{ $mi > 0 ? 'collapsed':'' }} fw-semibold"
              type="button" data-bs-toggle="collapse" data-bs-target="#mod{{ $module->id }}"
              style="font-size:.9rem;background:#fafafa">
              <i class="bi bi-folder2 me-2 text-warning"></i>
              {{ $module->title }}
              <span class="badge bg-secondary ms-2" style="font-size:.68rem">{{ $module->lessons->count() }}</span>
            </button>
          </h2>
          <div id="mod{{ $module->id }}" class="accordion-collapse collapse {{ $mi===0?'show':'' }}">
            <div class="accordion-body p-0">

              @foreach($module->lessons as $lesson)
              <div class="d-flex align-items-center gap-3 px-4 py-2 border-top" style="font-size:.85rem">
                @php $ico=['text'=>'bi-file-text-fill text-primary','video'=>'bi-play-circle-fill text-danger','pdf'=>'bi-file-pdf-fill text-danger','link'=>'bi-link-45deg text-info'][$lesson->type]??'bi-file-fill'; @endphp
                <i class="bi {{ $ico }}" style="font-size:1rem;width:18px;text-align:center;flex-shrink:0"></i>
                <span class="flex-grow-1">{{ $lesson->title }}</span>
                @if($lesson->duration_minutes)
                <span class="text-muted" style="font-size:.75rem;flex-shrink:0">{{ $lesson->duration_minutes }}min</span>
                @endif
                @if($enrolled)
                <a href="{{ route('lessons.show',[$course,$lesson]) }}" class="btn btn-sm btn-outline-success flex-shrink-0" style="font-size:.72rem">
                  <i class="bi bi-play-fill me-1"></i>Ver
                </a>
                @else
                <i class="bi bi-lock text-muted flex-shrink-0"></i>
                @endif
                @can('update',$course)
                <form method="POST" action="{{ route('lessons.destroy',[$course,$lesson]) }}" onsubmit="return confirm('¿Eliminar lección?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-link text-danger p-0 flex-shrink-0"><i class="bi bi-trash"></i></button>
                </form>
                @endcan
              </div>
              @endforeach

              @can('update',$course)
              <div class="px-4 py-2 border-top">
                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalAddLesson{{ $module->id }}">
                  <i class="bi bi-plus-circle me-1"></i>Agregar lección
                </button>
              </div>
              @endcan
            </div>
          </div>
        </div>

        {{-- Modal agregar lección --}}
        @can('update',$course)
        <div class="modal fade" id="modalAddLesson{{ $module->id }}" tabindex="-1">
          <div class="modal-dialog">
            <div class="modal-content" style="border-radius:12px">
              <div class="modal-header">
                <h5 class="modal-title fw-bold" style="font-size:.95rem">Agregar Lección — {{ $module->title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <form method="POST" action="{{ route('lessons.store',[$course,$module]) }}">
                @csrf
                <div class="modal-body">
                  <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:.84rem">Título *</label>
                    <input type="text" name="title" class="form-control" required>
                  </div>
                  <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:.84rem">Tipo *</label>
                    <select name="type" class="form-select" id="tipoSelect{{ $module->id }}" onchange="updateContentHelp({{ $module->id }},this.value)">
                      <option value="text">Texto / HTML</option>
                      <option value="video">Video (URL embed)</option>
                      <option value="pdf">PDF (URL)</option>
                      <option value="link">Enlace externo</option>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:.84rem">Contenido *</label>
                    <span id="helpText{{ $module->id }}" class="text-muted d-block mb-1" style="font-size:.75rem">Escribe el contenido HTML o pega la URL</span>
                    <textarea name="content" class="form-control" rows="4" required placeholder="Escribe el contenido..."></textarea>
                  </div>
                  <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:.84rem">Duración (minutos)</label>
                    <input type="number" name="duration_minutes" class="form-control" min="1" placeholder="Opcional">
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                  <button type="submit" class="btn btn-carder"><i class="bi bi-save me-1"></i>Guardar Lección</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        {{-- Botón eliminar módulo --}}
        @endcan

        @empty
        <div class="text-center py-4 text-muted" style="font-size:.85rem">
          <i class="bi bi-folder2-open d-block mb-2 fs-3"></i>
          Este curso no tiene módulos aún.
          @can('update',$course)
          <br><button class="btn btn-sm btn-outline-success mt-2" data-bs-toggle="modal" data-bs-target="#modalAddModule">Agregar primer módulo</button>
          @endcan
        </div>
        @endforelse
      </div>
    </div>

    {{-- EVALUACIONES --}}
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-clipboard-check me-2 text-warning"></i>Evaluaciones</span>
        @can('update',$course)
        <a href="{{ route('quizzes.create',$course) }}" class="btn btn-sm btn-outline-warning">
          <i class="bi bi-plus me-1"></i>Agregar Evaluación
        </a>
        @endcan
      </div>
      <div class="card-body p-0">
        @forelse($course->quizzes as $quiz)
        @php $passed=$quiz->hasPassed(auth()->id()); $attempts=$quiz->getAttemptsCountForUser(auth()->id()); @endphp
        <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
          <i class="bi {{ $passed?'bi-patch-check-fill text-success':'bi-patch-question-fill text-warning' }}" style="font-size:1.5rem;flex-shrink:0"></i>
          <div class="flex-grow-1">
            <div class="fw-semibold" style="font-size:.9rem">{{ $quiz->title }}</div>
            <div class="text-muted" style="font-size:.75rem">
              Mínimo: {{ $quiz->passing_score }}% · Máx. intentos: {{ $quiz->max_attempts }} · Usados: {{ $attempts }}
            </div>
          </div>
          @if($enrolled || auth()->user()->hasRole(['admin','instructor']))
          <a href="{{ route('quizzes.show',[$course,$quiz]) }}" class="btn btn-sm {{ $passed?'btn-outline-success':'btn-warning' }} flex-shrink-0">
            {{ $passed?'Ver resultado':'Iniciar quiz' }}
          </a>
          @endif
          @can('update',$course)
          <form method="POST" action="{{ route('quizzes.destroy',[$course,$quiz]) }}" onsubmit="return confirm('¿Eliminar esta evaluación?')">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger flex-shrink-0"><i class="bi bi-trash"></i></button>
          </form>
          @endcan
        </div>
        @empty
        <div class="text-center py-4 text-muted" style="font-size:.85rem">Sin evaluaciones.</div>
        @endforelse
      </div>
    </div>
  </div>

  {{-- COLUMNA LATERAL --}}
  <div class="col-lg-4">
    <div class="card" style="position:sticky;top:75px">

      @if($enrolled)
        <div class="card-body text-center">
          <div style="width:56px;height:56px;background:#e8f5ee;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto .8rem">
            <i class="bi bi-check-lg text-success fs-3"></i>
          </div>
          <h6 class="fw-bold">¡Estás inscrito!</h6>
          <div class="d-flex justify-content-between align-items-center mt-3 mb-1">
            <span style="font-size:.78rem;color:#64748b">Tu progreso</span>
            <span class="fw-bold text-success" style="font-size:.78rem">{{ $progress }}%</span>
          </div>
          <div class="progress mb-3"><div class="progress-bar" style="width:{{ $progress }}%"></div></div>
          @if($cert)
          <a href="{{ route('courses.certificate',$course) }}" class="btn btn-warning w-100 mb-2 fw-semibold">
            <i class="bi bi-award me-1"></i>Ver mi Certificado
          </a>
          @endif
        </div>
      @elseif(auth()->user()->hasRole('participant'))
        <div class="card-body text-center">
          <i class="bi bi-journal-bookmark-fill text-success" style="font-size:2.5rem"></i>
          <h6 class="fw-bold mt-2 mb-1">Inscríbete gratis</h6>
          <p class="text-muted mb-3" style="font-size:.82rem">Accede a lecciones y evaluaciones.</p>
          <form method="POST" action="{{ route('courses.enroll',$course) }}">
            @csrf
            <button class="btn btn-carder w-100 fw-bold py-2">
              <i class="bi bi-plus-circle me-2"></i>Inscribirme
            </button>
          </form>
        </div>
      @endif

      <hr class="m-0">
      <div class="card-body">
        <h6 class="fw-bold mb-3" style="font-size:.84rem;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Información</h6>
        <div class="d-flex flex-column gap-2" style="font-size:.83rem">
          <div class="d-flex justify-content-between"><span class="text-muted"><i class="bi bi-clock me-1"></i>Duración</span><strong>{{ $course->duration_hours }}h</strong></div>
          <div class="d-flex justify-content-between"><span class="text-muted"><i class="bi bi-folder2 me-1"></i>Módulos</span><strong>{{ $course->modules->count() }}</strong></div>
          <div class="d-flex justify-content-between"><span class="text-muted"><i class="bi bi-people me-1"></i>Participantes</span><strong>{{ $course->enrollments->count() }}</strong></div>
          <div class="d-flex justify-content-between"><span class="text-muted"><i class="bi bi-clipboard me-1"></i>Evaluaciones</span><strong>{{ $course->quizzes->count() }}</strong></div>
        </div>
      </div>

      @can('update',$course)
      <hr class="m-0">
      <div class="card-body d-flex gap-2">
        <a href="{{ route('courses.edit',$course) }}" class="btn btn-outline-primary btn-sm flex-grow-1"><i class="bi bi-pencil me-1"></i>Editar</a>
        <form method="POST" action="{{ route('courses.destroy',$course) }}" onsubmit="return confirm('¿Eliminar este curso? Se eliminará todo su contenido.')">
          @csrf @method('DELETE')
          <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
        </form>
      </div>
      @endcan
    </div>
  </div>
</div>

{{-- MODAL AGREGAR MÓDULO --}}
@can('update',$course)
<div class="modal fade" id="modalAddModule" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content" style="border-radius:12px">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" style="font-size:.95rem">Agregar Módulo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="{{ route('modules.store',$course) }}">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:.84rem">Título del módulo *</label>
            <input type="text" name="title" class="form-control" required placeholder="Ej: Módulo 1 — Introducción">
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:.84rem">Descripción</label>
            <textarea name="description" class="form-control" rows="2" placeholder="Descripción breve (opcional)"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-carder"><i class="bi bi-save me-1"></i>Guardar Módulo</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endcan

@push('scripts')
<script>
function updateContentHelp(moduleId, type) {
  const hints = {
    'text':  'Escribe el contenido en HTML o texto plano.',
    'video': 'Pega la URL de embed del video (YouTube, Vimeo, etc.)',
    'pdf':   'Pega la URL directa al archivo PDF.',
    'link':  'Pega el enlace externo completo (https://...).',
  };
  document.getElementById('helpText'+moduleId).textContent = hints[type] || '';
}
</script>
@endpush
@endsection
