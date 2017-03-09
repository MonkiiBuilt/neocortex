
@extends('layouts.app')

@section('title', 'Neocortex')

@section('content')

    <div id="main">
        <div class="items">
            <div class="item item__full item__active">
                <div class="item-iframe">
                    <iframe src="http://www.youtube.com/embed/{{ $item->details['vid_id'] }}?rel=0&hd=1&autoplay=1" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>

@endsection
