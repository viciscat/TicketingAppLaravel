@props(["bag" => null, "for"])
@php
    $errorBag = $bag != null ? $errors->$bag : $errors;
    $hidden = $errorBag->has($for) ? '' : 'hidden ';
@endphp
<div id="{{ $for }}-error" {{ $attributes->merge(['class' => $hidden.'font-13px error-message']) }}>
    <img src="{{ asset("images/icons/msg_error-0.png") }}" alt="Error" width="16" height="16">
    <span>
        @if($errorBag->has($for))
            {!! implode("<br/>", $errorBag->get($for)) !!}
        @endif
    </span>
</div>
