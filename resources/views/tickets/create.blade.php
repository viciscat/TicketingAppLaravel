@extends("layout.main")
<?php
$projects = [];
?>
@section("title")
    Ticket Wizard
@endsection
@section("content")
    <h4 style="flex-shrink: 0">Ticket Wizard</h4>
    <div class="window-content">
        <!-- FIXME THE ACTION -->
        <form id="create-ticket-form" class="basic-form" method="post">
            <div class="field-row-stacked">
                <label for="project">Target Project</label>
                <select id="project">
                    <option>Tempo</option>
                </select>
            </div>

            <div class="field-row-stacked">
                <label for="name">Ticket Name</label>
                <input id="name" type="text" name="name" value=""/>
            </div>

            <!-- Dropdowns -->
            <div class="field-row-stacked">
                <label for="ticket-kind">Ticket Kind</label>
                <select id="project">
                    <option>Tempo</option>
                </select>
            </div>
            <div class="field-row-stacked">
                <label for="priority">Priority</label>
                <select id="project">
                    <option>Tempo</option>
                </select>
            </div>
            <div class="field-row-stacked">
                <label for="ticket-type">Type</label>
                <select id="project">
                    <option>Tempo</option>
                </select>
            </div>
            <div class="field-row-stacked" style="width: 300px">
                <label for="description">Description</label>
                <textarea id="description" rows="8" name="description"></textarea>
            </div>
        </form>

    </div>
    <input form="create-ticket-form" type="submit" value="Create Ticket"/>
    <script>
        document.getElementById('create-ticket-form').addEventListener('submit', (e) => {
            let valid = true;
            valid &= checkInput('project', 'project-error', [emptyCondition("A ticket must be linked to a project!")]);
            valid &= checkInput('name', 'name-error', [emptyCondition("A ticket must have a name!")]);
            valid &= checkInput('ticket-kind', 'ticket-kind-error', [emptyCondition("A ticket must have a kind!")]);
            valid &= checkInput('priority', 'priority-error', [emptyCondition("A ticket must have to a priority!")]);
            if (!valid) e.preventDefault();
            return valid;
        })
    </script>
@endsection
