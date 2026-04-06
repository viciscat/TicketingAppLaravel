@extends("layout.main")

@section("title")
    Project Wizard
@endsection

@section("content")
    <h4 style="flex-shrink: 0">Project Wizard</h4>
    <div class="window-content">
        <form class="basic-form" id="create-project-form" method="post" enctype="multipart/form-data"
              action="{{ route('projects.update', $project->id) }}">
            @csrf
            @method('PUT')
            <div class="field-row-stacked">
                <label for="name">Project Name</label>
                <input id="name" type="text" name="name" value="{{ old('name') ?? $project->name }}"/>
                <x-input-error for="name"/>
            </div>
            <div class="field-row-stacked">
                <label for="issue-prefix">Issue Prefix</label>
                <input id="issue-prefix" maxlength="4" required style="max-width: 10ch" type="text" name="issue-prefix"
                       value="{{ old('issue-prefix') ?? $project->issue_prefix }}"/>
                <x-input-error for="issue-prefix" alt="Issue Prefix"/>
            </div>


            <div class="field-row-stacked">
                <label for="contract">Contract</label>
                <input accept="application/pdf" id="contract" type="file" value="Contract" name="contract" onchange="onFileSelectChange(this)"/>
                <x-input-error for="contract"/>
            </div>
            Upload a new contract to change these fields.
            <div class="field-row-stacked">
                <label for="included-hours">Included Hours</label>
                <input id="included-hours" name="included-hours" type="number" min="0"
                       value="{{ old('included-hours') ?? $project->contract->included_hours }}" disabled/>
                <x-input-error for="included-hours" alt="Included Hours"/>
            </div>
            <div class="field-row-stacked">
                <label for="extra-hourly-rate">Extra Hourly Rate</label>
                <input id="extra-hourly-rate" name="extra-hourly-rate" type="number" min="0" step="0.01"
                       value="{{ old('extra-hourly-rate') ?? $project->contract->extra_hourly_rate}}" disabled/>
                <x-input-error for="extra-hourly-rate"/>
            </div>
        </form>
    </div>
    <p>These options can be modified later on.</p>
    <input form="create-project-form" type="submit" value="Update Project"/>
    <script>

        function isFilePresent(input) {
            return input.files.length > 0 && input.files[0] != null && input.files[0] !== ""
        }

        function onFileSelectChange(input) {
            const enableInputs = isFilePresent(input);
            document.getElementById('included-hours').disabled = !enableInputs
            document.getElementById('extra-hourly-rate').disabled = !enableInputs
        }

        document.getElementById('create-project-form').addEventListener('submit', (e) => {
            let valid = true;
            valid &= checkInput('name', 'name-error', [emptyCondition("Name is required!")]);
            if (isFilePresent(document.getElementById('contract'))) {
                valid &= checkInput('contract', 'contract-error', [emptyCondition("A contract is required!")]);
                valid &= checkInput('included-hours', 'included-hours-error', [{
                    predicate: input => input.value >= 0,
                    message: "Included hours must be greater than 0!"
                }]);
                valid &= checkInput('extra-hourly-rate', 'extra-hourly-rate-error', [{
                    predicate: input => input.value >= 0,
                    message: "Extra hourly rate must be greater than 0!"
                }]);
            }

            valid &= checkInput('issue-prefix', 'issue-prefix-error', [emptyCondition("Issue prefix is required!"), lengthCondition(4, "Prefix must be at most 4 characters long!")]);
            if (!valid) e.preventDefault();
            return valid;
        })
    </script>
@endsection

