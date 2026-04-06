@props(['id', 'options', 'default' => '', 'placeholder' => ''])
<select name="{{$id}}" id="{{$id}}" {{$attributes}}>
    @if(empty($default) && !empty($placeholder))
        <option hidden value="" selected>{{$placeholder}}</option>
    @endif

    @foreach($options as $value => $display)
        <option value="{{$value}}" @selected($default == $value)>{{$display}}</option>
    @endforeach
</select>
