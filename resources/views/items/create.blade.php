
@extends('layouts.app')

@section('title', 'Neocortex - Create')

@section('headerJS')
    <script
            src="https://code.jquery.com/jquery-3.2.1.min.js"
            integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
            crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
@endsection

@section('content')

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

            <div class="panel panel-default">
                <div class="panel-heading">Add a random image</div>
                <div class="panel-body">
                    {!! Form::open(['route' => 'item.randomImage', 'class' => 'random-image-form form-horizontal']) !!}

                    <div class="form-group">
                        <label for="email" class="col-md-4 control-label"></label>

                        <div class="col-md-6">
                            <input type="hidden" name="randImgUrl" />

                            <button type="submit" class="btn btn-primary luckyBtn">
                                <i class='spinner glyphicon glyphicon-refresh spinning'></i> I'm feeling lucky
                            </button>

                        </div>
                    </div>

                    {!! Form::close() !!}
                </div>
            </div>

        </div>
    </div>

    <div id="randomImageModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Acceptable image?</h4>
                </div>
                <div class="modal-body">
                    <div class="the-image"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success rand-image-yeah">Hell yeah girfriend</button>
                    <button type="button" class="btn btn-danger rand-image-no"><i class='spinner glyphicon glyphicon-refresh spinning'></i> Get this rubbish outa my face</button>
                </div>
            </div>

        </div>
    </div>

@endsection
