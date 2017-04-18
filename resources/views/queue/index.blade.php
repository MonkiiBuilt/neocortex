
@extends('layouts.app')

@section('title', 'Neocortex - Item Queue')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Item queue</div>
                <div class="panel-body">

                    <div class="queue">
                        @foreach($items as $item)
                            @php

                            @endphp
                            <div class="queue-item" data-id="{{ $item->id }}">
                                <div class="queue-check">
                                    <input type="checkbox" name="queued" value="Queued" {{ (!$item->trashed()) ? 'checked="checked"' : "" }}>
                                </div>

                                <div class="queue-content">
                                    @if (View::exists("items.partials.{$item->type}"))
                                        @include("items.partials.{$item->type}", ['item' => $item])
                                    @endif
                                </div>

                                <div class="queue-duration">
                                    <input type="number" name="duration" max= "1800000" min= "1000" step= "1000" value="{{ $item->details['duration'] }}"> milliseconds
                                </div>
                            </div>
                            <hr>
                        @endforeach
                    </div>

                    <div class="queue__pagination">
                        {{ $items->links('queue.partials.pagination') }}
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection
