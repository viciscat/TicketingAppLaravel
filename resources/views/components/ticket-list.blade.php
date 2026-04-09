@props(['project_id' => -1, 'assignedToMe' => false, 'defaultStatus'=> "all"])
@php
    use App\Enums\TicketStatus;
        use App\Enums\TicketType;
        $typeOptions = TicketType::keyToName();
        $statusOptions = TicketStatus::keyToName();
        $statusOptions = ["all" => 'All'] + $statusOptions;
        $typeOptions = ["all" => 'All'] + $typeOptions;
@endphp
<div {{ $attributes }}>
    <div class="flex-row gap-6">
        <div class="field-stacked">
            <label for="ticket-type-filter">Ticket Type</label>
            <x-select id="ticket-type-filter" :options="$typeOptions" onchange="updateList()"/>
        </div>
        <div class="field-stacked">
            <label for="ticket-status-filter">Ticket Status</label>
            <x-select id="ticket-status-filter" :options="$statusOptions" onchange="updateList()" :default="$defaultStatus"/>
        </div>
        <div class="field-stacked">
            <label for="search">Search</label>
            <input type="text" id="search" oninput="updateList()"/>
        </div>
    </div>
    <div class="sunken-panel list">
        <table id="tickets-table">
            <thead>
            <tr>
                <th class="fit">ID</th>
                <th>Title</th>
                <th>Type</th>
                <th onclick="prioritySortClick(this)" style="cursor: pointer; align-items: center">
                    <span>Priority</span>
                    <span style="float: right; font-size: 12px" id="arrow"></span>
                </th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <div class="status-bar margin-top-1">
            <p class="status-bar-field"></p>
            <div class="status-bar-field no-growth"><a onclick="changePage(-1)" class="fake"><<</a></div>
            <p class="status-bar-field no-growth" id="page-container">Page 1/1</p>
            <div class="status-bar-field no-growth"><a class="fake" onclick="changePage(1)">>></a></div>
            <p class="status-bar-field"></p>
        </div>
    </div>

</div>

@once
    <script>
        const typeInput = document.getElementById('ticket-type-filter');
        const statusInput = document.getElementById('ticket-status-filter');
        const searchInput = document.getElementById('search');
        const pageContainer = document.getElementById('page-container');
        const prioritySortOptions = ["none", "asc", "desc"];
        const prioritySortOptionsChar = ["", "⮟", "⮝"];
        const tableBody = document.querySelector("#tickets-table tbody")
        let priority = 0;
        let page = 1;
        let lastPage = 1;

        async function updateList() {
            const route = "{{ route("api.tickets.list") }}";
            const params = new URLSearchParams({
                'status': statusInput.value,
                'type': typeInput.value,
                'priority-sort': prioritySortOptions[priority],
                'search': searchInput.value,
                'page': page,
                @if($project_id >= 0) 'in-project': {{ $project_id }}, @endif
                @if($assignedToMe) 'assigned-to-me': true, @endif
            });
            const response = await fetch(
                route + "?" + params,
                {headers: csrf({'Accept': 'application/json'}), credentials: "same-origin"}
            )

            if (!response.ok) return;
            tableBody.innerHTML = "";
            const tickets = await response.json();
            lastPage = tickets['last_page'];
            page = Math.min(page, lastPage);
            pageContainer.innerHTML = "Page: " + page + "/" + lastPage;
            console.log(tickets);
            for (const ticket of tickets.data) {
                const row = document.createElement("tr");
                row.innerHTML = `
                <tr>
                    <td class="fit"><a href="${ticket['project_route']}"
                                       title="View Project">${ticket['slug']}</a>
                    </td>
                    <td>${ticket['title']}</td>
                    <td>${ticket['type']}</td>
                    <td>${ticket['priority']}</td>
                    <td>${ticket['status']}</td>
                    <td class="actions">
                        <a href="${ticket['ticket_route']}">[View]</a>
                    </td>
                </tr>
                `;
                tableBody.appendChild(row);
            }
        }

        function prioritySortClick(thing) {
            priority += 1;
            priority %= prioritySortOptions.length;
            thing.querySelector("#arrow").innerHTML = prioritySortOptionsChar[priority];
            updateList()
        }

        function changePage(direction) {
            page += direction;
            page = Math.max(1, page);
            page = Math.min(page, lastPage);
            updateList();
        }

        updateList();
    </script>
@endonce
