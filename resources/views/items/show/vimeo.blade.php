
@extends('layouts.app')

@section('title', 'Neocortex')

@section('content')

    <div id="main">
        <div class="items">
            <div class="item item__full item__active">
                <div class="item-iframe">
                    <iframe src="https://player.vimeo.com/video/{{ $item->details['vid_id'] }}?autoplay=1&badge=0" width="640" height="360" frameborder="0"></iframe>
                </div>
            </div>
        </div>
    </div>

@endsection
