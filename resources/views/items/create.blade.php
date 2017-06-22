
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

            <div class="panel panel-default">
                <div class="panel-heading">Upload an image</div>
                <div class="panel-body">
                    {!! Form::open(['route' => 'item.store', 'class' => 'form-horizontal', 'enctype' => 'multipart/form-data']) !!}

                    <div class="form-group{{ $errors->has('upload') ? ' has-error' : '' }}">
                        <label class="col-md-4 control-label">Image File</label>

                        <div class="col-md-6">
                            <label class="btn  btn-default  btn-file">
                                Browse <input name="upload" type="file" hidden>
                            </label>

                            <span class='label label-default' id="upload-file-info"></span>

                            @if ($errors->has('upload'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('upload') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="processing" class="col-md-4 control-label">Processing?</label>

                        <div class="col-md-6">
                            <select name="processing">
                                @foreach($processingOptions as $option)
                                    <option value="{{ $option['filter'] }}" data-desc="{{ $option['desc'] }}">
                                        {{ $option['title'] }}
                                    </option>
                                @endforeach
                            </select>

                            <div class="processing-desc  text-info">{{ $processingOptions[0]['desc'] }}</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-8 col-md-offset-4">
                            <input type="submit" name="btnUpload" class="btn btn-primary" value="Upload" />
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

@section('scripts')
    <script type="text/javascript">
        $(function() {
            // Alter Image Processing description on change
            $("select[name='processing']").change(function() {
                var $option = $(this).find("option:selected"),
                    $desc   = $(this).parent().find(".processing-desc");

                $desc.text($option.attr("data-desc"));
            });

            // Add image filename if selected to upload
            $("input[name='upload']").change(function() {
                var filename = $(this).val().split('\\').pop();
                $("#upload-file-info").text(filename);
            });
        });
    </script>
@endsection