@extends('layouts.app')
@section('content')
<div class="wrap">
  <div class="panel" style="max-width:460px;margin:60px auto">
    <span class="eyebrow">Dirección de carrera</span>
    <h2 class="mt">Acceso profesor</h2>
    <form method="POST" action="{{ route('profesor.login.store') }}">
      @csrf
      <div class="field"><label>Usuario</label><input name="user" placeholder="profesor@unab.edu.co"></div>
      <div class="field"><label>Contraseña</label><input name="pass" type="password" placeholder="••••••••"></div>
      @foreach($errors->all() as $e)<p class="err">{{ $e }}</p>@endforeach
      <button class="btn" style="width:100%">Entrar</button>
    </form>
  </div>
</div>
@endsection