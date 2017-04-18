
@extends('layouts.app')

@section('title', 'Neocortex')

@section('content')

    <div id="main">
        <div class="items">
            <div class="item item__full item__active">
                @if (View::exists("items.partials.{$item->type}"))
                    @include("items.partials.{$item->type}", ['item' => $item])
                @else
                    <span>ERROR: Show type not found</span>
                @endif
            </div>
        </div>
    </div>

@endsection
