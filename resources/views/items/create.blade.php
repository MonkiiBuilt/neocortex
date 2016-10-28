
@extends('layouts.app')

@section('title', 'Neocortex - Create')

@section('content')

    {!! Form::open(['route' => 'item.store']) !!}
    {!! Form::token() !!}

    <div class="form-item">
        {!! Form::label('url', 'URL') !!}
        {!! Form::text('url', null, ['placeholder' => 'Enter a URL to share']) !!}
    </div>

    {!! Form::submit('Share') !!}

    {!! Form::close() !!}

@endsection
