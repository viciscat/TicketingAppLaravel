@extends("layout.main")
<?php
$projectInfo = null;
$contractInfo = null;
$collaborators = [];
$tickets = [];
?>
@section("title")
    Project Info
@endsection

@section("content")
        <script src="{{asset("js/ticketListFilter.js")}}"></script>
        <?php if (isset($projectInfo)) : ?>
        <div class="title-container">
            <img class="icon" src="{{asset("images/icons/computer-2.png")}}" alt="Ticket Icon"/>
            <div class="field-border" style="padding: 8px; width: 30%; min-width: 200px">
                <?= $projectInfo['name'] ?>
            </div>
        </div>
        <b>Collaborators:</b>
        <br/>
        <div class="sunken-panel small-sunken-table">
            <table>
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Role</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($collaborators as $collaborator) : ?>
                <tr>
                    <td><?= $collaborator["first_name"] . " " . $collaborator["last_name"] ?></td>
                    <td><?= $collaborator["role"] ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <b>Contract:</b>
        <div class="contract-container">
            <button onclick="location.href = 'actions/download_contract.php?id=<?=$projectInfo["contract_id"]?>'">View contract</button>
            <?php if (isset($contractInfo)):?>
            <span>Included hours: <?= $contractInfo["included_hours"] ?>h</span>
            <span>Extra Hourly Rate: <?= $contractInfo["extra_hourly_rate"] ?>€/h</span>
            <?php endif; ?>
        </div>
        <b>Tickets:</b>
        <div class="filter-row">
            <div class="field-stacked">
                <label for="ticket-type-filter">Ticket Type</label>
                <select id="ticket-type-filter" onchange="updateFilters()">
                    <option value="all">All</option>
                    <option value="included">Included</option>
                    <option value="billed">Billed</option>
                </select>
            </div>
            <div class="field-stacked">
                <label for="ticket-status-filter">Ticket Status</label>
                <select id="ticket-status-filter" onchange="updateFilters()">
                    <option value="all">All</option>
                    <option value="new">New</option>
                    <option value="in progress">In progress</option>
                    <option value="finished">Finished</option>
                </select>
            </div>
        </div>
        <div class="sunken-panel ticket-list">
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Title</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($tickets as $ticket) : ?>
                    <tr>
                        <td><?= $projectInfo["issue_prefix"] . "-" . $ticket["local_id"] ?></td>
                        <td><?= $ticket["type"] ?? "Unset" ?></td>
                        <td><?= $ticket["status"] ?></td>
                        <td><?= $ticket["name"] ?></td>
                        <td><a href="view.php?id=<?=$ticket['id']?>">[View]</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <a href="{{route("tickets.create")}}"><button>Create new ticket</button></a>
        <?php else : ?>
        Unknown project.
        <?php endif; ?>
@endsection
