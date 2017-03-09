
@extends('layouts.app')

@section('title', 'Neocortex - Create')

@section('content')

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Create from online image url</div>
                <div class="panel-body">
                    {!! Form::open(['route' => 'item.store', 'class' => 'form-horizontal']) !!}

                        <div class="form-group{{ $errors->has('url') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">URL</label>

                            <div class="col-md-6">
                                {!! Form::text('url', null, ['placeholder' => 'Enter a URL to share', 'class' => 'form-control']) !!}

                                @if ($errors->has('url'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('url') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Share
                                </button>
                            </div>
                        </div>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>

    {{--<div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Create from uploaded image</div>
                <div class="panel-body">
                    {!! Form::open(['route' => 'item.store', 'class' => 'form-horizontal']) !!}

                    <div class="form-group{{ $errors->has('url') ? ' has-error' : '' }}">
                        <label for="email" class="col-md-4 control-label">URL</label>

                        <div class="col-md-6">
                            {!! Form::text('url', null, ['placeholder' => 'Enter a URL to share', 'class' => 'form-control']) !!}

                            @if ($errors->has('url'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('url') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-8 col-md-offset-4">
                            <button type="submit" class="btn btn-primary">
                                Share
                            </button>
                        </div>
                    </div>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>--}}

@endsection
