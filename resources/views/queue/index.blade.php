
@extends('layouts.app')

@section('title', 'Neocortex - Item Queue')

@section('headerJS')
    <script
            src="https://code.jquery.com/jquery-3.2.1.min.js"
            integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
            crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Item queue</div>
                <div class="panel-body">

                    @include('flash::message')

                    <table class="queue-table">

                        <tr>
                            <td>Type</td>
                            <td>Preview</td>
                            <td>Actions</td>
                        </tr>

                        @foreach($entries as $entry)

                            @php
                            $item =& $entry->item;
                            @endphp

                            <tr data-id="{{ $entry->id }}">

                                <td>
                                    {{ $item->type }}
                                </td>

                                <td>
                                    @if (View::exists("items.partials.{$item->type}"))
                                        @include("items.partials.{$item->type}", ['item' => $item])
                                    @endif
                                </td>

                                <td>
                                    {!! Form::open(['route' => ['queue.destroy', $entry->id], 'method' => 'DELETE']) !!}
                                        <input type="submit" value="Remove">
                                    {!! Form::close() !!}
                                </td>
                            </tr>
                        @endforeach
                    </table>

                </div>
            </div>
        </div>
    </div>

    <div id="imagePreviewModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Image preview</h4>
                </div>
                <div class="modal-body">
                    <div class="the-image"></div>
                </div>
                {{--<div class="modal-footer">--}}
                    {{--<button type="button" class="btn btn-success rand-image-yeah">Hell yeah girfriend</button>--}}
                    {{--<button type="button" class="btn btn-danger rand-image-no"><i class='spinner glyphicon glyphicon-refresh spinning'></i> Get this rubbish outa my face</button>--}}
                {{--</div>--}}
            </div>

        </div>
    </div>

@endsection
