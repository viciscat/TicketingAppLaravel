@extends("layout.main")

@section("title")
    Project Wizard
@endsection

@section("content")
    <h4 style="flex-shrink: 0">Project Wizard</h4>
    <div class="window-content">
        <!-- FIXME ACTION -->
        <form class="basic-form" id="create-project-form" method="post" enctype="multipart/form-data">
            <div class="field-row-stacked">
                <label for="name">Project Name</label>
                <input id="name" type="text" name="name" value=""/>
            </div>
            <div class="field-row-stacked">
                <label for="issue-prefix">Issue Prefix</label>
                <input id="issue-prefix" maxlength="4" required style="max-width: 10ch" type="text" name="issue-prefix"
                       value=""/>
            </div>


            <div class="field-row-stacked">
                <label for="contract">Contract</label>
                <input accept="application/pdf" id="contract" required type="file" value="Contract" name="contract"/>
            </div>
            <div class="field-row-stacked">
                <label for="included-hours">Included Hours</label>
                <input id="included-hours" name="included-hours" type="number" min="0"
                       value=""/>
            </div>
            <div class="field-row-stacked">
                <label for="extra-hourly-rate">Extra Hourly Rate</label>
                <input id="extra-hourly-rate" name="extra-hourly-rate" type="number" min="0" step="0.01"
                       value=""/>
            </div>
            <div class="field-row-stacked">
                <input type="hidden" id="collaborators" name="collaborators"/>
                <span>Collaborators</span>
                <div class="collaborators-input-container">
                    <div class="sunken-panel" style="min-height: 8rem; min-width: 150px">
                        <table id="collaborators-table">
                            <thead>
                            <tr>
                                <th>Email</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div>
                        <div class="field-row">
                            <label for="collaborator-email">Email</label>
                            <input id="collaborator-email" type="email"/>
                        </div>
                        <input id="add-collaborator" onclick="addCollaborator()" type="button"
                               value="Add collaborator"/>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <p>These options can be modified later on.</p>
    <input form="create-project-form" type="submit" value="Create Project"/>
    <script>
        let collaborators = []

        function addCollaborator() {
            let collaborator = document.getElementById('collaborator-email');
            let valid = true;

            valid &= checkInput(collaborator, 'collaborator-email-error', [
                emptyCondition("Collaborator must not be empty!"),
                emailCondition("Email is invalid!"),
                {
                    predicate: input => !(input.value in collaborators),
                    message: "Collaborator already exists!"
                }
            ]);

            if (valid) {
                let collaboratorTable = document.querySelector("#collaborators-table tbody");
                const row = `
                        <tr>
                            <td>${collaborator.value}</td>
                            <td><a>[X]</a></td>
                        </tr>
                        `
                collaboratorTable.insertAdjacentHTML('beforeend', row);
                collaborators.push(collaborator.value);
                collaborator.value = ""
            }
        }

        document.getElementById('create-project-form').addEventListener('submit', (e) => {
            let valid = true;
            valid &= checkInput('name', 'name-error', [emptyCondition("Name is required!")]);
            valid &= checkInput('contract', 'contract-error', [emptyCondition("A contract is required!")]);
            valid &= checkInput('included-hours', 'included-hours-error', [{
                predicate: input => input.value >= 0,
                message: "Included hours must be greater than 0!"
            }]);
            valid &= checkInput('extra-hourly-rate', 'extra-hourly-rate-error', [{
                predicate: input => input.value >= 0,
                message: "Extra hourly rate must be greater than 0!"
            }]);

            valid &= checkInput('issue-prefix', 'prefix-error', [emptyCondition("Issue prefix is required!"), lengthCondition(4, "Prefix must be at most 4 characters long!")]);
            document.getElementById("collaborators").value = collaborators.join(";");
            if (!valid) e.preventDefault();
            return valid;
        })
    </script>
@endsection
