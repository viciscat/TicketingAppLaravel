@extends("layout.main")
<?php
$tickets = [];
?>
@section("title")
    Tickets
@endsection

@section("content")
        <h4>Tickets</h4>
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
                    <!-- FIXME LINKS -->
                    <td><a href="../projects/view.blade.php?id=<?=$ticket['project_id']?>" title="View Project"><?= $ticket["issue_prefix"] . "-" . $ticket["local_id"] ?></a></td>
                    <td><?= $ticket["type"] ?? "Unset" ?></td>
                    <td><?= $ticket["status"] ?></td>
                    <td><?= $ticket["name"] ?></td>
                    <td><a href="view.blade.php?id=<?=$ticket['id']?>">[View]</a></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <a href="{{route("tickets.create")}}">
            <button>Create new ticket</button>
        </a>
@endsection
