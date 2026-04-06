@php use App\Enums\UserRole; @endphp
@extends("layout.main")
@section("title")
    Dashboard
@endsection
@section("content")
    <div style="margin: 16px; align-items: stretch" class="flex-row gap-8">
        @foreach($statistics as $statistic)
            <x-dashboard-statistic :widget="$statistic"/>
        @endforeach
    </div>
    @if($role != UserRole::CLIENT)
        <div>
            <p>
            <b>Your assigned tickets:</b>
            </p>
            <x-ticket-list assigned-to-me/>
        </div>
    @endif
    <div>
        <p>
            <b>Ticket count by status:</b>
        </p>
        <div class="sunken-panel" style="width: fit-content">
            <table>
                <thead>
                <tr>
                    <th>Status</th>
                    <th>Count</th>
                </tr>
                </thead>
                <tbody>
                @foreach($countByStatus as $c)
                    <tr>
                        <td>{{ $c['status'] }}</td>
                        <td>{{ $c['count'] }}</td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>
    </div>


@endsection
