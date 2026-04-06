@php
    use App\Enums\TicketKind;
    use App\Enums\TicketPriority;use App\Enums\TicketType;

    $projectOptions = [];
    foreach ($projects as $project) {
        $projectOptions[$project->id] = $project->name;
    }
@endphp
@extends("layout.main")
@section("title")
    Ticket Wizard
@endsection
@section("content")
    <h4 style="flex-shrink: 0">Ticket Wizard</h4>
    <div class="window-content">
        <form id="create-ticket-form" class="basic-form" method="post" action="{{route("tickets.store")}}">
            @csrf
            <div class="field-row-stacked">
                <label for="project">Target Project</label>
                <x-select id="project" placeholder="Select Project" :options="$projectOptions"
                          :default="request()->get('project') ?? old('project')"/>
                <x-input-error for="project"/>
            </div>

            <div class="field-row-stacked">
                <label for="title">Ticket Title</label>
                <input id="title" type="text" name="title" value=""/>
                <x-input-error for="title"/>
            </div>


            <!-- Dropdowns -->
            <div class="field-row-stacked">
                <label for="ticket-kind">Ticket Kind</label>
                <x-select id="ticket-kind" placeholder="Select Kind" :options="TicketKind::keyToName()"
                          :default="old('ticket-kind')"/>
                <x-input-error for="ticket-kind"/>
            </div>
            <div class="field-row-stacked">
                <label for="priority">Priority</label>
                <x-select id="priority" placeholder="Select Priority" :options="TicketPriority::keyToName()"
                          :default="old('priority')"/>
                <x-input-error for="priority" alt="Priority"/>
            </div>
            <div class="field-row-stacked">
                <div class="field-row-stacked">
                    <label for="ticket-type">Type</label>
                    <x-select id="ticket-type" placeholder="Select Type" :options="TicketType::keyToName()"
                              :default="old('ticket-type')"/>
                    <x-input-error for="ticket-type"/>
                </div>
                <p>
                    A billed ticket needs to be validated by the client.
                </p>
            </div>
            <div class="field-row-stacked" style="width: 300px">
                <label for="description">Description</label>
                <textarea id="description" rows="8" name="description">{{ old("description") }}</textarea>
            </div>
        </form>

    </div>
    <input form="create-ticket-form" type="submit" value="Create Ticket"/>
    <script>
        document.getElementById('create-ticket-form').addEventListener('submit', (e) => {
            let valid = true;
            valid &= checkInput('project', 'project-error', [emptyCondition("A ticket must be linked to a project!")]);
            valid &= checkInput('title', 'title-error', [emptyCondition("A ticket must have a title!")]);
            valid &= checkInput('ticket-kind', 'ticket-kind-error', [emptyCondition("A ticket must have a kind!")]);
            valid &= checkInput('ticket-type', 'ticket-type-error', [emptyCondition("A ticket must have a type!")]);
            valid &= checkInput('priority', 'priority-error', [emptyCondition("A ticket must have to a priority!")]);
            if (!valid) e.preventDefault();
            return valid;
        })
    </script>
@endsection
