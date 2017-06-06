
@extends('layouts.app')

@section('title', 'Neocortex - Item Queue')

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

@endsection
