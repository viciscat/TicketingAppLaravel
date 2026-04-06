@extends("layout.main")
@section("title")
    Ticket Info
@endsection

@section("content")
    <div class="title-container">
        <div class="ticket-icon-number-container">
            <img class="icon" src="{{ asset("images/icons/ticket.png") }}" alt="Ticket Icon"/>
            <span>{{ $ticket->project->issue_prefix . "-" . $ticket->local_id }}</span>
        </div>
        <div class="field-border" style="padding: 8px; width: 30%; min-width: 200px">
            {{ $ticket->title }}
        </div>
        <div>
            {{ $ticket->kind->getName() }}
        </div>

    </div>
    <div class="flex-row gap-4 margin-top-1">
        <img src="{{ asset("images/icons/user_computer-0.png") }}" alt="Creator">
        <span>Created by {{  $ticket->createdBy->fullName() }}</span>
    </div>
    <div class="flex-row gap-4 margin-bottom-1">
        <img src="{{ asset("images/icons/users-1.png") }}" alt="Users">
        <span>
            <span>Assigned to</span>
            <span id="assigned-to">
             @php
            $assigned = $ticket->assignedTo()->get();
            if (empty($assigned)) {
                echo "no one.";
            } else {
                echo implode(", ", $assigned->map(function ($c) {
                    $fullName = $c->fullName();
                    $r = route("profile.other", $c->id);
                    return "<a href='$r'>$fullName</a>";
                })->toArray());
            }
            @endphp
                </span>
        </span>
    </div>

    <div style="gap: 4px; display: flex; flex-wrap: wrap">
        <span class="status-field-border" style="padding: 6px">
            Status: <span class="status-value">{{ $ticket->status->getName() }}</span>
        </span>
        <span class="status-field-border" style="padding: 6px">
            Priority: <span class="status-value"
                            style="color: {{ $ticket->priority->getCssColor() }}">{{ $ticket->priority->getName() }}</span>
        </span>
        <span class="status-field-border" style="padding: 6px">
            Type: <span class="status-value" style="color: darkred">{{ $ticket->type?->getName() ?? "Unset" }}</span>
        </span>
        <span class="status-field-border" style="padding: 6px">
            Total Time Spent: <span class="status-value" style="color: darkred"> {{ $totalTime }} </span>
        </span>
    </div>
    <br/>
    <div class="field-border" style="padding: 8px; min-height: 10rem; max-width: 300px">
        {!! nl2br(e($ticket->description)) !!}
    </div>
    <div class="margin-y-1">
        <x-member-input id="assign-input" :in-project="$ticket->project_id" no-client />
        <button onclick="addMember()">Assign</button>
        <x-input-error for="assign-input"/>
    </div>
    <div class="flex-row gap-4 margin-y-1">
        <img src="{{ asset("images/icons/clock-1.png") }}" alt="Clock">
        <b>Time Tracking:</b>
    </div>
    <div class="sunken-panel small-sunken-table">
        <table>
            <thead>
            <tr>
                <th>Name</th>
                <th>Time Spent</th>
            </tr>
            </thead>
            <tbody>
            @foreach( $logs as $name => $time )
                <tr>
                    <td>{{ $name }}</td>
                    <td>{{ $time }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div>
        <button onclick="location.href = '{{ route("tickets.log", $ticket->id) }}'">Track Time</button>
    </div>

    @if( $clientValidation )
        <form method="post" id="client-validation-form" class="basic-form">
            <div class="flex-row gap-4 margin-y-1">
                <img src="{{ asset("images/icons/msg_warning-2.png") }}" alt="Warning">
                <b>
                    This ticket is awaiting validation from you!
                </b>
                <img src="{{ asset("images/icons/msg_warning-2.png") }}" alt="Warning">
            </div>
            <p>
                One or multiple collaborators want to make this ticket billed. Do you accept?
            </p>
            <div class="field-row">
                <input id="accept" name="accept" type="checkbox" @checked(old('accept'))/>
                <label for="accept"><b>Yes</b>, make this ticket billed.</label>
            </div>
            <div class="field-row-stacked" style="width: 300px">
                <label for="reason">Reason if refused</label>
                <textarea id="reason" rows="8" name="reason" maxlength="500">{{ old("reason") }}</textarea>
            </div>
            <input type="submit" value="Submit"/>
        </form>
    @endif
    @if ( $ticket->status == \App\Enums\TicketStatus::REFUSED )
        <div>
            <div class="flex-row gap-4 margin-y-1">
                <img src="{{ asset("images/icons/msg_warning-2.png") }}" alt="Warning">
                <b>Refusal Reason</b>
            </div>
            <div class="field-border" style="padding: 8px; min-height: 5rem; max-width: 200px">
                {!! nl2br(e($ticket->refuse_reason)) !!}
            </div>
        </div>
        <br/>
    @endif
    <p>
        <b>Attachments: (UNIMPLEMENTED)</b>
    </p>
    <div class="sunken-panel small-sunken-table">
        <table>
            <thead>
            <tr>
                <th>File name</th>
                <th>Size</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>latest.log</td>
                <td>4.1ko</td>
                <td><a href="#">Download</a><span style="width: 8px; display: inline-block"></span><a
                        href="#">[x]</a></td>
            </tr>
            </tbody>
        </table>
    </div>
    @if( $editable )
        <button onclick="location.href = '{{ route("tickets.edit", $ticket->id) }}'">Edit</button>
    @endif
@endsection
@section('inline-script')
    <script>
        const memberInput = document.getElementById("assign-input");
        const memberInputError = document.getElementById("assign-input-error");
        const assignedTo = document.getElementById("assigned-to");
        const routeTemplate = "{{ route('profile.other', '%d') }}"

        async function addMember() {
            const response = await fetch("{{ route("api.tickets.assigned.store") }}", {
                method: "POST",
                headers: csrf({
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                }),
                body: JSON.stringify({
                    id: {{ $ticket->id }},
                    member_email: memberInput.value
                })
            })



            let result = await response.json()

            if (response.ok) {
                memberInputError.classList.add("hidden");
            } else {
                memberInputError.classList.remove("hidden");
                memberInputError.querySelector("span").innerHTML = result.errors.member_email.join("<br/>")
                return;
            }
            let member = result['member'];


            if (assignedTo.innerHTML === "no one.") {
                assignedTo.innerHTML = `<a href="${routeTemplate.replace("%d", member.id)}">${member.full_name}</a>`
            } else {
                assignedTo.innerHTML += `, <a href="${routeTemplate.replace("%d", member.id)}">${member.full_name}</a>`
            }

            membersTable.appendChild(row);
        }
    </script>
@endsection
