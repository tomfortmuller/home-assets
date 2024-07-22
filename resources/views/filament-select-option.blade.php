{{--<div class="flex rounded-md relative">--}}
{{--    <div class="flex">--}}
{{--        <div class="px-2 pt-2 pb-1">--}}
{{--            <div class="h-8 w-24">--}}
{{--                <img src="{{ url('/storage/'.$logo.'') }}" alt="{{ $name }} icon" role="img" class="h-full w-full overflow-hidden shadow object-cover" />--}}
{{--            </div>--}}
{{--        </div>--}}

{{--        <div class="flex flex-col justify-center pl-3 pt-2 pb-1">--}}
{{--            <p class="">{{ $name }}</p>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
<div class="flex rounded-md relative">
    <div class="flex">
        <div class="px-2">
            <div class="h-8 w-24">
                <img src="{{ url('/storage/'.$logo.'') }}" alt="{{ $name }} icon" role="img" class="h-full w-full overflow-hidden shadow object-cover" />
            </div>
        </div>

        <div class="flex flex-col justify-center pl-3 leading-none">
            <p class="">{{ $name }}</p>
        </div>
    </div>
</div>