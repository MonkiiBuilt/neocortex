
<div class="items">
@foreach ($items as $item)
    @if (View::exists("items.show.{$item->type}"))
        <div class="item item__full">
            @include("items.show.{$item->type}", ['item' => $item])
        </div>
    @endif
@endforeach
</div>
