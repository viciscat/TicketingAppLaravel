@extends("layout.main")
@section("title")
    Projects
@endsection

@section("content")
    <h4>Projects</h4>
    <div class="sunken-panel">
        <table>
            <thead>
            <tr>
                <th>Project Name</th>
                <th>Issue Prefix</th>
                <th>Open Issues</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($projects as $project)
                <tr>
                    <td>{{ $project["name"]  }}</td>
                    <td>{{ $project["issue_prefix"]  }}</td>
                    <td>{{ $project->tickets()->count()  }}</td>
                    <td><a href="{{ route('projects.view', $project["id"])  }}">[View]</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <button onclick="location.href = '{{route("projects.create")}}'">Create new project</button>
@endsection
