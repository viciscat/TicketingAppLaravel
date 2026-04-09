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
    @if ( !$projectsOvertime->isEmpty() )
        <div class="flex-row gap-4 margin-y-1">
            <img src="{{ asset("images/icons/msg_warning-0.png") }}" alt="Warning">
            <b>Some projects have used all their included time!</b>
        </div>
        <ul>
            @foreach( $projectsOvertime as $project )
                <li>
                    <a href="{{ route("projects.view", $project->id) }}">{{ $project->name }}</a>
                </li>
            @endforeach
        </ul>
    @endif


@endsection
