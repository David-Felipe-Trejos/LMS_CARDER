@extends('layouts.app')
@section('title','Crear Evaluación')
@section('page-title','Nueva Evaluación')
@section('content')

<div class="row justify-content-center"><div class="col-lg-9">

<div class="d-flex align-items-center gap-3 mb-4">
  <a href="{{ route('courses.show',$course) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
  <div>
    <h4 class="fw-bold mb-0">Crear Evaluación</h4>
    <p class="text-muted mb-0" style="font-size:.82rem">Curso: {{ $course->title }}</p>
  </div>
</div>

<form method="POST" action="{{ route('quizzes.store',$course) }}" id="quizForm">
@csrf

{{-- Datos generales --}}
<div class="card mb-4">
  <div class="card-header"><i class="bi bi-info-circle me-2 text-primary"></i>Datos Generales</div>
  <div class="card-body p-4">
    <div class="row g-3">
      <div class="col-12">
        <label class="form-label fw-semibold">Título de la evaluación *</label>
        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
               value="{{ old('title') }}" required placeholder="Ej: Quiz — Recurso Hídrico">
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="col-12">
        <label class="form-label fw-semibold">Descripción / Instrucciones</label>
        <textarea name="description" class="form-control" rows="2"
                  placeholder="Instrucciones para los participantes...">{{ old('description') }}</textarea>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold">Puntaje mínimo para aprobar (%) *</label>
        <input type="number" name="passing_score" class="form-control"
               value="{{ old('passing_score',70) }}" min="1" max="100" required>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold">Intentos máximos permitidos *</label>
        <input type="number" name="max_attempts" class="form-control"
               value="{{ old('max_attempts',3) }}" min="1" max="10" required>
      </div>
    </div>
  </div>
</div>

{{-- PREGUNTAS --}}
<div id="questionsContainer">
  {{-- Pregunta 1 por defecto --}}
  <div class="question-block card mb-3" data-q="1">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span class="fw-semibold" style="font-size:.9rem"><i class="bi bi-question-circle me-2 text-warning"></i>Pregunta 1</span>
      <button type="button" class="btn btn-sm btn-outline-danger remove-question" onclick="removeQuestion(this)" style="display:none">
        <i class="bi bi-trash"></i>
      </button>
    </div>
    <div class="card-body p-4">
      <div class="mb-3">
        <label class="form-label fw-semibold">Enunciado de la pregunta *</label>
        <input type="text" name="questions[0][text]" class="form-control" required
               placeholder="Escribe la pregunta aquí...">
      </div>
      <div class="mb-2">
        <label class="form-label fw-semibold">Opciones de respuesta *</label>
        <p class="text-muted mb-2" style="font-size:.78rem">
          <i class="bi bi-info-circle me-1"></i>Selecciona el radio de la opción correcta.
        </p>
      </div>
      <div class="options-container" id="opts-0">
        @for($i=0;$i<4;$i++)
        <div class="d-flex align-items-center gap-2 mb-2 option-row">
          <input type="radio" name="questions[0][correct]" value="{{ $i }}" class="form-check-input mt-0" style="flex-shrink:0" {{ $i===0?'required':'' }}>
          <input type="text" name="questions[0][options][]" class="form-control form-control-sm"
                 placeholder="Opción {{ $i+1 }}" required>
          @if($i>=2)
          <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.option-row').remove()" title="Eliminar opción">
            <i class="bi bi-x"></i>
          </button>
          @endif
        </div>
        @endfor
      </div>
      <button type="button" class="btn btn-sm btn-outline-secondary mt-1" onclick="addOption(0)">
        <i class="bi bi-plus me-1"></i>Agregar opción
      </button>
    </div>
  </div>
</div>

{{-- Botones --}}
<div class="d-flex gap-2 mb-4">
  <button type="button" class="btn btn-outline-warning" onclick="addQuestion()">
    <i class="bi bi-plus-circle me-1"></i>Agregar Pregunta
  </button>
  <button type="submit" class="btn btn-carder px-4">
    <i class="bi bi-save me-1"></i>Guardar Evaluación
  </button>
  <a href="{{ route('courses.show',$course) }}" class="btn btn-outline-secondary">Cancelar</a>
</div>

</form>
</div></div>

@push('scripts')
<script>
let qCount = 1; // ya hay 1

function addQuestion() {
  const qi = qCount;
  const html = `
  <div class="question-block card mb-3" data-q="${qi+1}">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span class="fw-semibold" style="font-size:.9rem"><i class="bi bi-question-circle me-2 text-warning"></i>Pregunta ${qi+1}</span>
      <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeQuestion(this)">
        <i class="bi bi-trash"></i>
      </button>
    </div>
    <div class="card-body p-4">
      <div class="mb-3">
        <label class="form-label fw-semibold">Enunciado *</label>
        <input type="text" name="questions[${qi}][text]" class="form-control" required placeholder="Escribe la pregunta aquí...">
      </div>
      <div class="mb-2">
        <label class="form-label fw-semibold">Opciones *</label>
        <p class="text-muted mb-2" style="font-size:.78rem"><i class="bi bi-info-circle me-1"></i>Selecciona la opción correcta.</p>
      </div>
      <div class="options-container" id="opts-${qi}">
        ${[0,1,2,3].map(i=>`
        <div class="d-flex align-items-center gap-2 mb-2 option-row">
          <input type="radio" name="questions[${qi}][correct]" value="${i}" class="form-check-input mt-0" style="flex-shrink:0" ${i===0?'required':''}>
          <input type="text" name="questions[${qi}][options][]" class="form-control form-control-sm" placeholder="Opción ${i+1}" required>
          ${i>=2?`<button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.option-row').remove()" title="Eliminar opción"><i class="bi bi-x"></i></button>`:''}
        </div>`).join('')}
      </div>
      <button type="button" class="btn btn-sm btn-outline-secondary mt-1" onclick="addOption(${qi})">
        <i class="bi bi-plus me-1"></i>Agregar opción
      </button>
    </div>
  </div>`;
  document.getElementById('questionsContainer').insertAdjacentHTML('beforeend', html);
  qCount++;
  updateRemoveButtons();
}

function removeQuestion(btn) {
  btn.closest('.question-block').remove();
  renumberQuestions();
  updateRemoveButtons();
}

function renumberQuestions() {
  document.querySelectorAll('.question-block').forEach((block, i) => {
    block.querySelector('.card-header span').innerHTML =
      `<i class="bi bi-question-circle me-2 text-warning"></i>Pregunta ${i+1}`;
  });
}

function updateRemoveButtons() {
  const blocks = document.querySelectorAll('.question-block');
  blocks.forEach((b,i) => {
    const btn = b.querySelector('.remove-question, .btn-outline-danger[onclick*=removeQuestion]');
    if (btn) btn.style.display = blocks.length > 1 ? '' : 'none';
  });
}

function addOption(qi) {
  const container = document.getElementById(`opts-${qi}`);
  const optCount  = container.querySelectorAll('.option-row').length;
  if (optCount >= 6) { alert('Máximo 6 opciones por pregunta.'); return; }
  const html = `
  <div class="d-flex align-items-center gap-2 mb-2 option-row">
    <input type="radio" name="questions[${qi}][correct]" value="${optCount}" class="form-check-input mt-0" style="flex-shrink:0">
    <input type="text" name="questions[${qi}][options][]" class="form-control form-control-sm" placeholder="Opción ${optCount+1}" required>
    <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.option-row').remove()"><i class="bi bi-x"></i></button>
  </div>`;
  container.insertAdjacentHTML('beforeend', html);
}

// Validar antes de enviar
document.getElementById('quizForm').addEventListener('submit', function(e) {
  const questions = document.querySelectorAll('.question-block');
  let valid = true;
  questions.forEach((block, i) => {
    const options = block.querySelectorAll('.option-row input[type=text]');
    const checked = block.querySelector('input[type=radio]:checked');
    if (options.length < 2) {
      alert(`La pregunta ${i+1} debe tener al menos 2 opciones.`);
      valid = false;
    }
    if (!checked) {
      alert(`Selecciona la respuesta correcta en la pregunta ${i+1}.`);
      valid = false;
    }
  });
  if (!valid) e.preventDefault();
});
</script>
@endpush
@endsection
