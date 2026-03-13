@extends("layout.main")
@section("title")
    Dashboard
@endsection
@section("content")
    UNIMPLEMENTED
    <div style="display: flex; flex-direction: row; gap: 8px; flex-wrap: wrap; margin: 16px">
        <div class="dashboard-statistic window">
            <img alt="Icon" class="statistic-icon" src="{{asset("images/icons/ticket.png")}}"/>

            <div class="statistic-text">
                <div class="statistic-title">New Tickets</div>
                <div class="statistic-number">10</div>
            </div>
        </div>
        <div class="dashboard-statistic window">
            <img alt="Icon" class="statistic-icon" src="{{asset("images/icons/ticket_due.png")}}"/>

            <div class="statistic-text">
                <div class="statistic-title">Tickets Past Due</div>
                <div class="statistic-number">1</div>
            </div>
        </div>
    </div>
    <b>Your assigned tickets:</b>
    <div class="sunken-panel ticket-list">
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Status</th>
                <th>Title</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><a href="#">SKB-1</a></td>
                <td>In progress</td>
                <td>Ticket Name Go Here :)</td>
                <td><a href="tickets/view.blade.php">[View]</a></td>
            </tr>
            </tbody>
        </table>
    </div>
@endsection
