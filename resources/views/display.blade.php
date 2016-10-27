
@extends('layouts.app')

@section('title', 'Neocortex')

@section('content')

    @if (Auth::user())
        <h1>Welcome back, {{ Auth::user()->name }}!</h1>
    @endif

    <p>Welcome to Neocortex.</p>

@endsection

