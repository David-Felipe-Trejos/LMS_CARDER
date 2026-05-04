@extends('layouts.app')
@section('title', $quiz->title)
@section('page-title', 'Evaluación')
@section('content')

{{-- Resultado flash --}}
@if(session('quiz_result'))
@php $r = session('quiz_result'); @endphp
<div class="alert {{ $r['passed'] ? 'alert-success' : 'alert-warning' }} alert-dismissible fade show mb-4 d-flex align-items-center gap-3">
  <i class="bi {{ $r['passed'] ? 'bi-patch-check-fill' : 'bi-patch-exclamation-fill' }}" style="font-size:2rem;flex-shrink:0"></i>
  <div>
    <div class="fw-bold" style="font-size:1rem">
      {{ $r['passed'] ? '🎉 ¡Aprobaste!' : '📚 No aprobaste esta vez' }}
    </div>
    <div style="font-size:.88rem">
      Obtuviste <strong>{{ $r['score'] }}%</strong> ({{ $r['correct'] }} de {{ $r['total'] }} correctas).
      {{ $r['passed'] ? 'Excelente trabajo.' : "Puntaje mínimo requerido: {$quiz->passing_score}%." }}
    </div>
  </div>
  <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-4">

  {{-- FORMULARIO DE PREGUNTAS --}}
  <div class="col-lg-8">

    <div class="card mb-4">
      <div class="card-body p-4">
        <h4 class="fw-bold mb-1">{{ $quiz->title }}</h4>
        @if($quiz->description)
        <p class="text-muted mb-3" style="font-size:.88rem">{{ $quiz->description }}</p>
        @endif
        <div class="d-flex flex-wrap gap-3" style="font-size:.8rem;color:#64748b">
          <span><i class="bi bi-question-circle me-1"></i>{{ $quiz->questions->count() }} preguntas</span>
          <span><i class="bi bi-trophy me-1"></i>Mínimo: {{ $quiz->passing_score }}%</span>
          <span><i class="bi bi-arrow-repeat me-1"></i>{{ $quiz->max_attempts }} intentos máx. (usados: {{ $attempts }})</span>
        </div>
      </div>
    </div>

    @if($canAttempt)
    <form method="POST" action="{{ route('quizzes.submit',[$course,$quiz]) }}" id="quizForm">
      @csrf

      @foreach($quiz->questions as $qi => $question)
      <div class="card mb-3" id="qcard-{{ $qi }}" style="border-left:3px solid #1e6b3a">
        <div class="card-body p-4">
          <div class="d-flex gap-2 mb-3">
            <span style="background:#1e6b3a;color:#fff;border-radius:50%;width:26px;height:26px;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;flex-shrink:0;margin-top:2px">
              {{ $qi+1 }}
            </span>
            <p class="fw-semibold mb-0" style="font-size:.92rem">{{ $question->question_text }}</p>
          </div>
          <div class="ps-4">
            @foreach($question->options as $option)
            <label class="quiz-option" id="opt-label-{{ $option->id }}" onclick="selectOption({{ $question->id }}, {{ $option->id }}, {{ $qi }})">
              <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}"
                     id="opt-{{ $option->id }}" class="form-check-input mt-0" style="flex-shrink:0">
              <span style="font-size:.88rem">{{ $option->option_text }}</span>
            </label>
            @endforeach
          </div>
        </div>
      </div>
      @endforeach

      <div class="d-flex gap-2 mt-2">
        <button type="submit" class="btn btn-carder px-4 fw-bold"
                onclick="return checkAllAnswered()">
          <i class="bi bi-send-fill me-2"></i>Enviar Evaluación
        </button>
        <a href="{{ route('courses.show',$course) }}" class="btn btn-outline-secondary">Cancelar</a>
      </div>
    </form>

    @else
    <div class="card">
      <div class="card-body text-center py-5">
        @if($lastAttempt && $lastAttempt->passed)
          <i class="bi bi-patch-check-fill text-success" style="font-size:3.5rem"></i>
          <h5 class="fw-bold mt-3">¡Ya aprobaste esta evaluación!</h5>
          <p class="text-muted">Obtuviste {{ $lastAttempt->score }}% en tu mejor intento.</p>
        @else
          <i class="bi bi-x-circle-fill text-danger" style="font-size:3.5rem"></i>
          <h5 class="fw-bold mt-3">Has agotado todos tus intentos</h5>
          <p class="text-muted">Usaste los {{ $quiz->max_attempts }} intentos permitidos.</p>
        @endif
        <a href="{{ route('courses.show',$course) }}" class="btn btn-outline-secondary btn-sm mt-2">
          <i class="bi bi-arrow-left me-1"></i>Volver al curso
        </a>
      </div>
    </div>
    @endif

  </div>

  {{-- SIDEBAR --}}
  <div class="col-lg-4">

    {{-- Navegación de preguntas --}}
    @if($canAttempt)
    <div class="card mb-3">
      <div class="card-body p-3">
        <div class="fw-semibold mb-2" style="font-size:.82rem;color:#64748b">Preguntas respondidas</div>
        <div class="d-flex flex-wrap gap-1">
          @foreach($quiz->questions as $qi => $q)
          <button type="button" id="nav-{{ $q->id }}" onclick="scrollToQuestion({{ $qi }})"
                  style="width:34px;height:34px;border-radius:6px;border:1px solid #e2e8f0;background:#f8fafc;font-size:.78rem;font-weight:700;color:#64748b;cursor:pointer;transition:all .15s">
            {{ $qi+1 }}
          </button>
          @endforeach
        </div>
        <div class="mt-2 text-muted" style="font-size:.75rem" id="answeredCount">
          0 de {{ $quiz->questions->count() }} respondidas
        </div>
      </div>
    </div>
    @endif

    {{-- Info del quiz --}}
    <div class="card mb-3">
      <div class="card-body p-3">
        <h6 class="fw-bold mb-3" style="font-size:.84rem;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Información</h6>
        <div class="d-flex flex-column gap-2" style="font-size:.82rem">
          <div class="d-flex justify-content-between">
            <span class="text-muted">Preguntas</span>
            <strong>{{ $quiz->questions->count() }}</strong>
          </div>
          <div class="d-flex justify-content-between">
            <span class="text-muted">Puntaje mínimo</span>
            <strong>{{ $quiz->passing_score }}%</strong>
          </div>
          <div class="d-flex justify-content-between">
            <span class="text-muted">Intentos usados</span>
            <strong>{{ $attempts }} / {{ $quiz->max_attempts }}</strong>
          </div>
        </div>
      </div>
    </div>

    {{-- Último intento --}}
    @if($lastAttempt)
    <div class="card">
      <div class="card-body p-3 text-center">
        <div class="text-muted mb-1" style="font-size:.75rem">Último intento</div>
        <div class="fw-bold {{ $lastAttempt->passed ? 'text-success' : 'text-danger' }}" style="font-size:2rem;font-family:'Nunito',sans-serif">
          {{ $lastAttempt->score }}%
        </div>
        <span class="badge {{ $lastAttempt->passed ? 'text-bg-success' : 'text-bg-danger' }}" style="font-size:.72rem">
          {{ $lastAttempt->passed ? 'Aprobado' : 'No aprobado' }}
        </span>
        <div class="text-muted mt-1" style="font-size:.72rem">{{ $lastAttempt->created_at->format('d/m/Y H:i') }}</div>
      </div>
    </div>
    @endif

  </div>
</div>

@push('scripts')
<script>
const answeredQuestions = new Set();
const totalQuestions    = {{ $quiz->questions->count() }};

function selectOption(questionId, optionId, qi) {
  // Reset estilos de todas las opciones del mismo grupo
  document.querySelectorAll(`[name="answers[${questionId}]"]`).forEach(radio => {
    document.getElementById('opt-label-' + radio.value).classList.remove('selected');
  });
  // Marcar seleccionada
  document.getElementById('opt-label-' + optionId).classList.add('selected');
  document.getElementById('opt-' + optionId).checked = true;
  // Actualizar navegación lateral
  const navBtn = document.getElementById('nav-' + questionId);
  if (navBtn) {
    navBtn.style.background    = '#1e6b3a';
    navBtn.style.color         = '#fff';
    navBtn.style.borderColor   = '#1e6b3a';
  }
  answeredQuestions.add(questionId);
  document.getElementById('answeredCount').textContent =
    answeredQuestions.size + ' de ' + totalQuestions + ' respondidas';
}

function scrollToQuestion(qi) {
  const card = document.getElementById('qcard-' + qi);
  if (card) card.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function checkAllAnswered() {
  if (answeredQuestions.size < totalQuestions) {
    const missing = totalQuestions - answeredQuestions.size;
    return confirm(`Hay ${missing} pregunta(s) sin responder. ¿Deseas enviar de todas formas?`);
  }
  return true;
}
</script>
@endpush
@endsection
