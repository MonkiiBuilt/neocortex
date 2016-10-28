
@extends('layouts.app')

@section('title', 'Neocortex')

@section('content')

    <img src="{{ $item->details['url'] }}">

@endsection
