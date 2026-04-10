@php use App\Enums\TicketStatus;use App\Enums\UserRole; @endphp
@extends("layout.main")
@section("title")
    Ticket Info
@endsection

@section("content")
    <div class="status-bar margin-bottom-1">
        <p class="status-bar-field">New -> In Progress -> Finished</p>
    </div>
    <div class="title-container">
        <div class="ticket-icon-number-container">
            <img class="icon" src="{{ asset("images/icons/ticket.png") }}" alt="Ticket Icon"/>
            <span><a href="{{ route("projects.view", $ticket->project->id) }}">{{ $ticket->project->issue_prefix . "-" . $ticket->local_id }}</a></span>
        </div>
        <div class="field-border" style="padding: 8px; width: 30%; min-width: 200px">
            {{ $ticket->title }}
        </div>
        <div>
            {{ $ticket->kind->getName() }}
        </div>

    </div>
    <!-- Created by -->
    <div class="flex-row gap-4 margin-top-1">
        <img src="{{ asset("images/icons/user_computer-0.png") }}" alt="Creator">
        <span>
            <span>Created by</span>
            <a href="{{ route('profile.other', $ticket->createdBy->id) }}">{{  $ticket->createdBy->fullName() }}</a>
        </span>

    </div>
    <!-- Assigned to -->
    <div class="flex-row gap-4 margin-bottom-1">
        <img src="{{ asset("images/icons/users-1.png") }}" alt="Users">
        <span>
            <span>Assigned to</span>
            <span id="assigned-to">
                {{ $ticket->assignedTo()->get()->isEmpty() ? "no one." : ":" }}
            </span>
        </span>
    </div>
    <ul id="assigned-to-list">
        @foreach( $ticket->assignedTo as $collaborator )
            <li>
                <a href="{{ route("profile.other", $collaborator->id) }}">{{ $collaborator->fullName() }}</a>
                @if ( $editable )
                    <a class="fake" style="margin-left: 8px"
                       onclick="removeMember({{ $collaborator->id }}, this.parentNode)">[x]</a>
                @endif
            </li>
        @endforeach
    </ul>
    <!-- Assign to -->
    @if ( $editable )
        <div class="margin-y-1">
            <x-member-input id="assign-input" :in-project="$ticket->project_id" no-client/>
            <button onclick="addMember()">Assign</button>
            <x-input-error for="assign-input"/>
        </div>
    @endif

    <!-- Thingies -->
    <div class="flex-row gap-4">
        <span class="status-field-border ticket-info-thing">
            <b>Status:</b> <span class="status-value">{{ $ticket->status->getName() }}</span>
        </span>
        <span class="status-field-border ticket-info-thing">
            <b>Priority:</b> <span class="status-value"
                            style="color: {{ $ticket->priority->getCssColor() }}">{{ $ticket->priority->getName() }}</span>
        </span>
        <span class="status-field-border ticket-info-thing">
            <b>Type:</b> <span class="status-value">{{ $ticket->type?->getName() ?? "Unset" }}</span>
        </span>
        <span class="status-field-border ticket-info-thing">
            <b>Total Time Spent:</b> <span class="status-value"> {{ $totalTime }} </span>
        </span>
    </div>
    <br/>
    <div class="field-border" style="padding: 8px; min-height: 10rem; max-width: 300px">
        {!! nl2br(e($ticket->description)) !!}
    </div>

    <div class="flex-row gap-4 margin-y-1">
        <img src="{{ asset("images/icons/clock-1.png") }}" alt="Clock">
        <b>Time Tracking:</b>
    </div>
    <div class="indent">
        <div class="sunken-panel small-sunken-table">
            <table>
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Time Spent</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach( $logs as $log )
                    <tr>
                        <td>{{ $log['full_name'] }}</td>
                        <td>{{ $log['time_spent'] }}</td>
                        <td class="actions"><a class="fake"
                                               onclick="showPopup({{ $log['id'] }}, '{{ $log['full_name'] }}')">[Details]</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @if (auth()->user()->role != UserRole::CLIENT)
            <div>
                <button onclick="location.href = '{{ route("tickets.log", $ticket->id) }}'">Track Time</button>
            </div>
        @endif
    </div>

    @if( $clientValidation )
        <form method="post" id="client-validation-form" class="basic-form indent"
              action="{{ route("tickets.client.validation", $ticket->id) }}">
            @csrf
            <div class="flex-row gap-4 margin-y-1">
                <img src="{{ asset("images/icons/msg_warning-0.png") }}" alt="Warning">
                <b>
                    This ticket is awaiting validation from you!
                </b>
                <img src="{{ asset("images/icons/msg_warning-0.png") }}" alt="Warning">
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
    @if ( $ticket->status == TicketStatus::REFUSED )
        <div>
            <div class="flex-row gap-4 margin-y-1">
                <img src="{{ asset("images/icons/msg_warning-0.png") }}" alt="Warning">
                <b>Client refused making this ticket billed!</b>
            </div>
            <div class="indent">
                <p>Reason provided:</p>
                <div class="field-border" style="padding: 8px; min-height: 5rem; max-width: 300px">
                    {!! nl2br(e($ticket->refuse_reason)) !!}
                </div>
            </div>
        </div>
    @endif
    @if( $editable )
        <p>
            <b>Ticket actions:</b>
        </p>
        <div>
            <button onclick="location.href = '{{ route("tickets.edit", $ticket->id) }}'">Edit</button>
            <form action="{{route('tickets.destroy')}}" method="post" style="display: inline">
                @csrf
                @method('DELETE')
                <input type="hidden" name="id" value="{{ $ticket->id }}">
                <input type="submit" value="Delete"/>
            </form>
        </div>
    @endif
    <dialog id="logs-dialog" closedby="any">
        <div class="window">
            <div class="title-bar">
                <div class="title-bar-text" id="popup-title">
                    Lorem ipsum
                </div>
                <div class="title-bar-controls">
                    <button aria-label="Close" onclick="document.getElementById('logs-dialog').close()"></button>
                </div>
            </div>
            <div class="window-body">
                <div class="sunken-panel">
                    <table>
                        <thead>
                        <tr>
                            <th>Time Spent</th>
                            <th>Comment</th>
                        </tr>
                        </thead>
                        <tbody id="details-body"></tbody>
                    </table>
                </div>
                <div class="margin-top-1">
                    <button aria-label="Close" onclick="document.getElementById('logs-dialog').close()">Close</button>
                </div>
            </div>
        </div>
    </dialog>
@endsection
@section('inline-script')
    <script>
        const memberInput = document.getElementById("assign-input");
        const memberInputError = document.getElementById("assign-input-error");
        const assignedTo = document.getElementById("assigned-to");
        const assignedToList = document.getElementById("assigned-to-list");
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
                assignedTo.innerHTML = ":"
            }
            const li = document.createElement("li");
            li.innerHTML += `<a href="${routeTemplate.replace("%d", member.id)}">${member.full_name}</a>`
            assignedToList.appendChild(li);

            membersTable.appendChild(row);
        }

        async function removeMember(memberId, li) {
            await fetch("{{route("api.tickets.assigned.store")}}", {
                method: "DELETE",
                headers: csrf({
                    'Content-Type': 'application/json',
                }),
                credentials: 'same-origin',
                body: JSON.stringify({
                    id: {{ $ticket->id }},
                    member_id: memberId,
                })
            })
            li.remove();
        }

        const logsDialog = document.getElementById("logs-dialog");
        const detailsBody = document.getElementById("details-body");
        const popupTitle = document.getElementById("popup-title");

        async function showPopup(userId, fullName) {
            let route = "{{ route("api.tickets.logs") }}";
            let params = new URLSearchParams({
                user_id: userId,
                ticket_id: {{ $ticket->id }},
            });
            popupTitle.innerHTML = fullName + "'s logs";
            const response = await fetch(
                route + "?" + params,
                {headers: csrf({'Accept': 'application/json'}), credentials: "same-origin"}
            )
            const json = await response.json();
            console.log(json);
            if (!response.ok) return;
            detailsBody.innerHTML = "";
            for (const log of json) {
                const row = document.createElement("tr");
                row.innerHTML = `
                <td>${log.time_spent}</td>
                <td style="text-wrap: auto">${log.comment}</td>
                `;
                detailsBody.appendChild(row);
            }

            logsDialog.showModal();

        }
    </script>
@endsection
