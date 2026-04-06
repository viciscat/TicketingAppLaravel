@props(["noClient" => false, "inProject" => -1])
@php
    $datalistId = "suggestions-" . $attributes->string('id');
@endphp

<input type="email" {{ $attributes }} autocomplete="off" list="{{$datalistId}}" />
<datalist id="{{$datalistId}}"></datalist>
<script>
    const _memberInput = document.getElementById("{{ $attributes->string('id') }}");
    const memberSuggestions = document.getElementById("{{$datalistId}}");
    let timeOutFunction;
    _memberInput.addEventListener("input", function () {
        clearTimeout(timeOutFunction);
        timeOutFunction = setTimeout(async function () {
            let route = "{{ route("api.users.suggestions") }}";
            let params = new URLSearchParams({
                input: _memberInput.value,
                @if($inProject >= 0) 'in_project_id': {{ $inProject }}, @endif
                @if($noClient) 'no-client': true, @endif
            });
            const response = await fetch(
                route + "?" + params,
                {headers: csrf({'Accept': 'application/json'}), credentials: "same-origin"}
            )
            memberSuggestions.innerHTML = "";
            for (const user of await response.json()) {
                let opt = document.createElement("option");
                opt.setAttribute("value", user.email);
                opt.setAttribute("label", user.full_name);
                memberSuggestions.appendChild(opt);
            }
        }, 200)

    })
</script>
