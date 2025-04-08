@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @if (session()->has('usuario'))
        <h1 class="text-center mt-5">¡Bienvenido al Dashboard!</h1>
        <p class="text-center">Usuario conectado: <strong>{{ session('usuario') }}</strong></p>

        <div class="text-center mt-3">
            <a href="{{ route('logout') }}" class="btn btn-danger">Cerrar sesión</a>
        </div>
    @else
    @php
    if (!session()->has('usuario')) {
        header('Location: ' . route('login'));
        exit;
    }
    @endphp
    @endif
@endsection

