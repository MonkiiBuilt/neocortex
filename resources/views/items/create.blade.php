
@extends('layouts.app')

@section('title', 'Neocortex - Create')

@section('content')
<?php
    //dd($errors);
?>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Create from online url</div>
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
                                <input type="submit" name="btnUrl" class="btn btn-primary" value="Share" />
                            </div>
                        </div>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>

@endsection
