
@extends('layouts.app')

@section('title', 'Neocortex')

@section('content')


    <div>
        @each('partials.item', $items, 'item')
    </div>

@endsection
