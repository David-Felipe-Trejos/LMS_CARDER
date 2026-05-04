<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Iniciar Sesión — LMS CARDER</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Nunito:wght@800;900&display=swap" rel="stylesheet">
  <style>
    body{font-family:'Inter',sans-serif;min-height:100vh;background:linear-gradient(135deg,#0d2d1a,#1a2e45);display:flex;align-items:center;justify-content:center;padding:1rem}
    .lcard{background:#fff;border-radius:1.25rem;padding:2.2rem;max-width:430px;width:100%;box-shadow:0 24px 60px rgba(0,0,0,.4)}
    .form-control:focus{border-color:#1e6b3a;box-shadow:0 0 0 .2rem rgba(30,107,58,.15)}
    .btn-login{background:#1e6b3a;color:#fff;font-weight:700;padding:.75rem;border-radius:.6rem;font-size:.95rem;border:none;width:100%}
    .btn-login:hover{background:#145229;color:#fff}
    .demo-pill{background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);border-radius:.6rem;padding:.5rem .9rem;color:rgba(255,255,255,.8);font-size:.78rem;cursor:pointer;transition:all .2s;text-align:left;width:100%;margin-bottom:.35rem;display:block}
    .demo-pill:hover{background:rgba(255,255,255,.15);color:#fff}
  </style>
</head>
<body>
<div style="max-width:440px;width:100%">
  <div class="lcard mb-3">
    <div class="d-flex align-items-center gap-3 mb-4">
      <div style="width:48px;height:48px;background:#1e6b3a;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="bi bi-tree-fill text-white fs-4"></i></div>
      <div><div style="font-family:'Nunito',sans-serif;font-weight:900;font-size:1.15rem;color:#0d2d1a">LMS CARDER</div><div style="font-size:.75rem;color:#64748b">Corporación Autónoma Regional de Risaralda</div></div>
    </div>
    <h5 style="font-weight:700;color:#1e293b;margin-bottom:1.2rem">Iniciar Sesión</h5>
    @if($errors->any())<div class="alert alert-danger py-2 mb-3" style="font-size:.85rem;border-radius:.6rem"><i class="bi bi-exclamation-circle me-1"></i>{{ $errors->first() }}</div>@endif
    <form method="POST" action="{{ route('login') }}">
      @csrf
      <div class="mb-3">
        <label class="form-label fw-semibold" style="font-size:.84rem">Correo electrónico</label>
        <div class="input-group"><span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span><input type="email" name="email" class="form-control border-start-0 ps-1" value="{{ old('email') }}" placeholder="usuario@carder.gov.co" required autofocus></div>
      </div>
      <div class="mb-4">
        <label class="form-label fw-semibold" style="font-size:.84rem">Contraseña</label>
        <div class="input-group"><span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span><input type="password" name="password" id="pwdInput" class="form-control border-start-0 border-end-0 ps-1" placeholder="••••••••" required><button type="button" class="btn btn-outline-secondary border-start-0" onclick="togglePwd()"><i class="bi bi-eye" id="pwdIcon"></i></button></div>
      </div>
      <div class="form-check mb-4"><input class="form-check-input" type="checkbox" name="remember" id="rem"><label class="form-check-label" for="rem" style="font-size:.83rem">Recordarme</label></div>
      <button type="submit" class="btn-login"><i class="bi bi-box-arrow-in-right me-2"></i>Ingresar al Sistema</button>
    </form>
  </div>
  <div style="background:rgba(255,255,255,.07);border-radius:1rem;padding:1rem">
    <p style="color:rgba(255,255,255,.45);font-size:.7rem;text-transform:uppercase;letter-spacing:1.2px;margin-bottom:.6rem">Cuentas demo — contraseña: <strong style="color:rgba(255,255,255,.7)">password</strong></p>
    <button class="demo-pill" onclick="fillLogin('admin@carder.gov.co')"><i class="bi bi-shield-fill-check text-warning me-1"></i>Administrador CARDER</button>
    <button class="demo-pill" onclick="fillLogin('instructor@carder.gov.co')"><i class="bi bi-person-badge-fill text-info me-1"></i>Instructora — Juliana Ríos</button>
    <button class="demo-pill" onclick="fillLogin('instructor2@carder.gov.co')"><i class="bi bi-person-badge-fill text-info me-1"></i>Instructor — Carlos Mejía</button>
    <button class="demo-pill" onclick="fillLogin('ana.torres@carder.gov.co')"><i class="bi bi-mortarboard-fill text-success me-1"></i>Participante — Ana Torres</button>
    <button class="demo-pill" onclick="fillLogin('participante@carder.gov.co')"><i class="bi bi-mortarboard-fill text-success me-1"></i>Participante CARDER</button>
  </div>
  <p class="text-center mt-3" style="color:rgba(255,255,255,.25);font-size:.72rem">CARDER © {{ date('Y') }} · Pereira, Risaralda · Colombia</p>
</div>
<script>
function fillLogin(e){document.querySelector('[name=email]').value=e;document.querySelector('[name=password]').value='password';}
function togglePwd(){var i=document.getElementById('pwdInput'),ico=document.getElementById('pwdIcon');i.type=i.type==='password'?'text':'password';ico.className=i.type==='password'?'bi bi-eye':'bi bi-eye-slash';}
</script>
</body>
</html>