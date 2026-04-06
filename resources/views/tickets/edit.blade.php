@php
    use App\Enums\TicketKind;
    use App\Enums\TicketPriority;
    use App\Enums\TicketType;
@endphp
@extends("layout.main")
@section("title")
    Ticket Edition Wizard
@endsection
@section("content")
    <h4 style="flex-shrink: 0">Edit Ticket</h4>
    <div class="window-content">
        <form id="edit-ticket-form" class="basic-form" method="post" action="{{route("tickets.update", $ticket->id)}}">
            @csrf
            @method('PUT')

            <div class="field-row-stacked">
                <label for="title">Ticket Title</label>
                <input id="title" type="text" name="title" value="{{old('title') ?? $ticket->title}}"/>
            </div>

            <!-- Dropdowns -->
            <div class="field-row-stacked">
                <label for="ticket-kind">Ticket Kind</label>
                <x-select id="ticket-kind" placeholder="Select Kind" :options="TicketKind::keyToName()"
                          :default="old('ticket-kind') ?? $ticket->kind->value"/>
            </div>
            <div class="field-row-stacked">
                <label for="priority">Priority</label>
                <x-select id="priority" placeholder="Select Priority" :options="TicketPriority::keyToName()"
                          :default="old('priority') ?? $ticket->priority->value"/>
            </div>
            <div class="field-row-stacked">
                <label for="ticket-type">Type</label>
                <x-select id="ticket-type" placeholder="Select Type" :options="TicketType::keyToName()" :default="old('ticket-type') ?? $ticket->type?->value ?? ''"/>
            </div>
            <div class="field-row-stacked" style="width: 300px">
                <label for="description">Description</label>
                <textarea id="description" rows="8"
                          name="description">{{old("description") ?? $ticket->description}}</textarea>
            </div>
        </form>

    </div>
    <input form="edit-ticket-form" type="submit" value="Edit Ticket"/>
    <script>
        document.getElementById('edit-ticket-form').addEventListener('submit', (e) => {
            let valid = true;
            valid &= checkInput('project', 'project-error', [emptyCondition("A ticket must be linked to a project!")]);
            valid &= checkInput('title', 'title-error', [emptyCondition("A ticket must have a title!")]);
            valid &= checkInput('ticket-kind', 'ticket-kind-error', [emptyCondition("A ticket must have a kind!")]);
            valid &= checkInput('priority', 'priority-error', [emptyCondition("A ticket must have to a priority!")]);
            if (!valid) e.preventDefault();
            return valid;
        })
    </script>
@endsection
