<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title','LMS CARDER')</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Nunito:wght@700;800;900&display=swap" rel="stylesheet">
  <style>
    :root{--green:#1e6b3a;--green-dark:#145229;--green-lite:#e8f5ee;--blue:#1a4d6e;--sw:255px;--th:60px}
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:'Inter',sans-serif;background:#f1f5f9;color:#1e293b}
    #sidebar{position:fixed;top:0;left:0;bottom:0;width:var(--sw);background:linear-gradient(180deg,#0d2d1a,#1a3a26);z-index:1000;overflow-y:auto;overflow-x:hidden;display:flex;flex-direction:column}
    .sb-logo{display:flex;align-items:center;gap:.75rem;padding:1.2rem 1rem;border-bottom:1px solid rgba(255,255,255,.08);flex-shrink:0}
    .sb-logo .ico{width:38px;height:38px;background:var(--green);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .sb-logo .t1{font-family:'Nunito',sans-serif;font-weight:900;font-size:1rem;color:#fff}
    .sb-logo .t2{font-size:.62rem;color:rgba(255,255,255,.4)}
    .nav-sec{padding:.5rem .75rem .2rem;font-size:.6rem;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:rgba(255,255,255,.3);margin-top:.5rem}
    .nav-item{display:flex;align-items:center;gap:.6rem;padding:.55rem .9rem;border-radius:8px;margin:.1rem .5rem;color:rgba(255,255,255,.65);font-size:.84rem;font-weight:500;text-decoration:none;transition:all .15s;border:none;background:transparent;width:calc(100% - 1rem);text-align:left;cursor:pointer}
    .nav-item:hover{background:rgba(255,255,255,.08);color:#fff}
    .nav-item.active{background:var(--green);color:#fff}
    .nav-item i{font-size:1rem;width:18px;text-align:center;flex-shrink:0}
    .sb-foot{padding:.75rem .5rem;border-top:1px solid rgba(255,255,255,.08);margin-top:auto;flex-shrink:0}
    #main{margin-left:var(--sw);min-height:100vh;display:flex;flex-direction:column}
    #topbar{height:var(--th);background:#fff;border-bottom:1px solid #e2e8f0;position:sticky;top:0;z-index:900;display:flex;align-items:center;justify-content:space-between;padding:0 1.5rem}
    .pg-title{font-family:'Nunito',sans-serif;font-weight:800;font-size:1.1rem;color:var(--green-dark)}
    .u-pill{display:flex;align-items:center;gap:.6rem;cursor:pointer;padding:.35rem .75rem;border-radius:30px;background:#f8fafc;border:1px solid #e2e8f0;transition:all .15s}
    .u-pill:hover{background:var(--green-lite);border-color:var(--green)}
    .av-sm{width:32px;height:32px;background:var(--green);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:.8rem;flex-shrink:0}
    .page-content{padding:1.5rem;flex:1}
    .stat-card{border-radius:14px;padding:1.2rem 1.4rem;color:#fff;position:relative;overflow:hidden}
    .stat-number{font-family:'Nunito',sans-serif;font-size:2rem;font-weight:900;line-height:1}
    .stat-label{font-size:.78rem;opacity:.85;margin-top:.2rem}
    .stat-icon{position:absolute;right:1rem;top:50%;transform:translateY(-50%);font-size:2.8rem;opacity:.2}
    .card{border:1px solid #e2e8f0;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,.04)}
    .card-header{background:#fff;border-bottom:1px solid #e2e8f0;font-weight:600;font-size:.88rem;padding:.85rem 1.2rem;border-radius:12px 12px 0 0!important}
    .course-card{border-radius:12px;overflow:hidden;border:1px solid #e2e8f0;background:#fff;transition:transform .2s,box-shadow .2s;height:100%;display:flex;flex-direction:column}
    .course-card:hover{transform:translateY(-3px);box-shadow:0 8px 24px rgba(0,0,0,.1)}
    .progress{height:7px;border-radius:4px}.progress-bar{background:var(--green)}
    .alert{border:none;border-radius:10px}
    .btn-carder{background:var(--green);color:#fff;font-weight:600;border:none}
    .btn-carder:hover{background:var(--green-dark);color:#fff}
    .form-control:focus,.form-select:focus{border-color:var(--green);box-shadow:0 0 0 .2rem rgba(30,107,58,.15)}
    .table{font-size:.85rem}
    .table thead th{font-weight:700;font-size:.78rem;text-transform:uppercase;letter-spacing:.5px;color:#64748b;border-bottom:2px solid #e2e8f0}
    .quiz-option{display:flex;align-items:center;gap:.8rem;padding:.7rem 1rem;border:2px solid #e2e8f0;border-radius:10px;cursor:pointer;transition:all .15s;margin-bottom:.4rem}
    .quiz-option:hover{border-color:var(--green);background:var(--green-lite)}
    .quiz-option.selected{border-color:var(--green);background:var(--green-lite)}
    @media(max-width:768px){#sidebar{width:0}#main{margin-left:0}#sidebar.open{width:var(--sw)}}
    @media print{#sidebar,#topbar,.no-print{display:none!important}#main{margin-left:0!important}}
  </style>
  @stack('styles')
</head>
<body>
<nav id="sidebar">
  <div class="sb-logo">
    <div class="ico"><i class="bi bi-tree-fill text-white fs-5"></i></div>
    <div><div class="t1">LMS CARDER</div><div class="t2">Sistema de Aprendizaje</div></div>
  </div>
  <div class="flex-grow-1" style="overflow-y:auto">
    <div class="nav-sec">Principal</div>
    <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard')?'active':'' }}">
      <i class="bi bi-grid-1x2-fill"></i>Dashboard
    </a>
    <a href="{{ route('courses.index') }}" class="nav-item {{ request()->routeIs('courses.index')||request()->routeIs('courses.show')?'active':'' }}">
      <i class="bi bi-collection-play-fill"></i>Cursos
    </a>
    @role('admin|instructor')
    <div class="nav-sec">Gestión</div>
    <a href="{{ route('courses.create') }}" class="nav-item {{ request()->routeIs('courses.create')?'active':'' }}">
      <i class="bi bi-plus-circle-fill"></i>Crear Curso
    </a>
    <a href="{{ route('reports.index') }}" class="nav-item {{ request()->routeIs('reports.*')?'active':'' }}">
      <i class="bi bi-bar-chart-fill"></i>Reportes
    </a>
    @endrole
    @role('admin')
    <div class="nav-sec">Administración</div>
    <a href="{{ route('users.index') }}" class="nav-item {{ request()->routeIs('users.index')?'active':'' }}">
      <i class="bi bi-people-fill"></i>Usuarios
    </a>
    <a href="{{ route('users.create') }}" class="nav-item {{ request()->routeIs('users.create')?'active':'' }}">
      <i class="bi bi-person-plus-fill"></i>Nuevo Usuario
    </a>
    @endrole
    <div class="nav-sec">Cuenta</div>
    <a href="{{ route('profile.edit') }}" class="nav-item {{ request()->routeIs('profile.*')?'active':'' }}">
      <i class="bi bi-person-circle"></i>Mi Perfil
    </a>
  </div>
  <div class="sb-foot">
    <form method="POST" action="{{ route('logout') }}">@csrf
      <button type="submit" class="nav-item" style="color:rgba(255,255,255,.55)">
        <i class="bi bi-box-arrow-left"></i>Cerrar Sesión
      </button>
    </form>
  </div>
</nav>
<div id="main">
  <div id="topbar">
    <div class="d-flex align-items-center gap-3">
      <button class="btn btn-sm btn-outline-secondary d-md-none" onclick="document.getElementById('sidebar').classList.toggle('open')">
        <i class="bi bi-list fs-5"></i>
      </button>
      <h1 class="pg-title mb-0">@yield('page-title','Dashboard')</h1>
    </div>
    <div class="d-flex align-items-center gap-2">
      <form action="{{ route('courses.index') }}" method="GET" class="d-none d-md-flex">
        <div class="input-group input-group-sm" style="width:200px">
          <input type="text" name="search" class="form-control" placeholder="Buscar cursos..." value="{{ request('search') }}">
          <button class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>
        </div>
      </form>
      <div class="dropdown">
        <div class="u-pill" data-bs-toggle="dropdown">
          <div class="av-sm">{{ auth()->user()->initial }}</div>
          <div class="d-none d-md-block">
            <div style="font-size:.82rem;font-weight:600;color:#1e293b">{{ Str::limit(auth()->user()->name,20) }}</div>
            <div style="font-size:.7rem;color:#64748b;text-transform:capitalize">{{ auth()->user()->getRoleNames()->first() }}</div>
          </div>
          <i class="bi bi-chevron-down" style="font-size:.7rem;color:#94a3b8"></i>
        </div>
        <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:10px;border:1px solid #e2e8f0;min-width:180px">
          <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>Mi Perfil</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><form method="POST" action="{{ route('logout') }}">@csrf
            <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-left me-2"></i>Cerrar Sesión</button>
          </form></li>
        </ul>
      </div>
    </div>
  </div>
  <div class="page-content">
    @if(session('success'))<div class="alert alert-success alert-dismissible fade show mb-3 d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill fs-5"></i><span>{{ session('success') }}</span><button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button></div>@endif
    @if(session('error'))<div class="alert alert-danger alert-dismissible fade show mb-3 d-flex align-items-center gap-2"><i class="bi bi-exclamation-circle-fill fs-5"></i><span>{{ session('error') }}</span><button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button></div>@endif
    @if($errors->any())<div class="alert alert-danger alert-dismissible fade show mb-3"><i class="bi bi-exclamation-triangle-fill me-2"></i><ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
    @yield('content')
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@stack('scripts')
</body>
</html>
