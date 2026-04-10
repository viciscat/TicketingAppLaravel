@extends("layout.main")
@section("title")
    Project Info
@endsection

@section("content")
    <div class="title-container">
        <img class="icon" src="{{asset("images/icons/computer-2.png")}}" alt="Ticket Icon"/>
        <div class="field-border" style="padding: 8px; width: 30%; min-width: 200px">
            {{ $project['name'] }}
        </div>
    </div>
    <div class="flex-row gap-4 margin-y-1">
        <img src="{{ asset("images/icons/users-0.png") }}" alt="Collaborators">
        <span><b>Collaborators</b></span>
    </div>
    <div class="indent">
        <div class="sunken-panel small-sunken-table">
            <table id="members-table">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Role</th>
                    @if ( $canEdit )
                        <th>Actions</th>
                    @endif
                </tr>
                </thead>
                <tbody>
                @foreach($project->members as $collaborator)
                    <tr>
                        <td>
                            <a href="{{ route('profile.other', $collaborator->id) }}">{{ $collaborator->fullName() }}</a>
                        </td>
                        <td>{{ $collaborator["role"]->getName() }}</td>
                        @if ( $canEdit )
                            <td class="actions">

                                <a class="fake"
                                   onclick="removeMember({{ $collaborator->id }}, this.parentNode.parentNode)">
                                    [x]
                                </a>

                            </td>
                        @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @if( $canEdit )
            <div>
                <p>Add member:</p>
                <x-member-input id="add-member-input"></x-member-input>
                <button onclick="addMember()">Add member</button>
            </div>
        @endif
    </div>
    <div class="flex-row gap-4 margin-y-1">
        <img src="{{ asset("images/icons/file_lines-0.png") }}" alt="Contract">
        <span><b>Contract</b></span>
    </div>
    <div class="flex-row gap-8 indent">
        <button onclick="location.href = '{{ route('contracts.view', $project->contract->id) }}'">
            View contract
        </button>
        <div class="flex-row gap-8">
            <span>Included hours: {{ $project->contract->included_hours }}h</span>
            <span>Extra Hourly Rate: {{ $project->contract->extra_hourly_rate }}€/h</span>
        </div>
    </div>
    <div class="flex-row gap-4 margin-y-1">
        <img src="{{ asset("images/icons/ticket.png") }}" alt="Ticket">
        <span><b>Tickets</b></span>
    </div>

    <div class="indent">
        <x-ticket-list :project_id="$project->id"/>
        @if( $canCreateTicket )
            <div>
                <button onclick="location.href = '{{ route('tickets.create', ["project" => $project->id]) }}'">
                    Create new ticket
                </button>
            </div>
        @endif
    </div>


    <div class="flex-row gap-4 margin-top-1">
        <img src="{{ asset("images/icons/clock-1.png") }}" alt="Clock">
        <span><b>Time Tracking</b></span>
    </div>
    <div class="indent">
        <p>
            <span>Contract time spent: </span>
            <span>{{ $timeIncluded }}</span>
            <br>
            <span>Billed time: </span>
            <span>{{ $timeBilled }}</span>
            <span>({{ $price }}€)</span>
        </p>
        @if( $overTime )
            <div class="flex-row gap-4">
                <img src="{{ asset("images/icons/msg_warning-2.png") }}" alt="Warning">
                <span><b>THIS PROJECT IS OVERTIME</b></span>
                <img src="{{ asset("images/icons/msg_warning-2.png") }}" alt="Warning">
            </div>
            <p>
                This project has spent all the time specified in the contract! <br/>
                Some tickets need to be switched to billed!
            </p>
        @endif
    </div>


    @if ( $canEdit )
        <p>
            <b>Project actions:</b>
        </p>
        <div>
            <button onclick="location.href = '{{ route('projects.edit', $project->id) }}'">Edit Project</button>
            <form action="{{route('projects.destroy')}}" method="post" style="display: inline">
                @csrf
                @method('DELETE')
                <input type="hidden" name="id" value="{{ $project->id }}">
                <input type="submit" value="Delete Project"/>
            </form>
        </div>
    @endif
@endsection
@section("inline-script")
    <script>
        const memberInput = document.getElementById("add-member-input");
        const membersTable = document.querySelector("#members-table tbody");

        async function addMember() {
            const response = await fetch("{{ route("api.projects.members.store") }}", {
                method: "POST",
                headers: csrf({
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                }),
                body: JSON.stringify({
                    id: {{ $project->id }},
                    member_email: memberInput.value
                })
            })

            console.log(response)

            let result = await response.json()
            let member = result['member'];


            const row = document.createElement("tr")
            row.innerHTML = `
                <td>${member.full_name}</td>
                <td>${member.role}</td>
                <td class="actions">
                    <a class="fake" onclick="removeMember(${member.id}, this.parentNode.parentNode)">[x]</a>
                </td>
            `

            membersTable.appendChild(row);
        }

        async function removeMember(memberId, row) {
            await fetch("{{route("api.projects.members.destroy")}}", {
                method: "DELETE",
                headers: csrf({
                    'Content-Type': 'application/json',
                }),
                credentials: 'same-origin',
                body: JSON.stringify({
                    id: {{ $project->id }},
                    member_id: memberId,
                })
            })
            row.remove();
        }


    </script>
@endsection
