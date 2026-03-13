@extends("layout.main")
<?php
$projects = [];
?>
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
                <?php foreach ($projects as $project): ?>
                <tr>
                    <td><?=$project["name"]?></td>
                    <td><?=$project["issue_prefix"]?></td>
                    <td><?=$project["open_ticket_count"]?></td>
                    <td><a href="view.blade.php?id=<?=$project["id"]?>">[View]</a></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <button onclick="location.href = '{{route("projects.create")}}'">Create new project</button>
@endsection
