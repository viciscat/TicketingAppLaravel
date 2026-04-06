@extends("layout.empty")
<?php
function button($route, $name): void
{
    $disabled = ($route == \Illuminate\Support\Facades\Route::currentRouteName()) ? "disabled" : "";
    $link = route($route);
    echo "<button onclick=\"location.href = '{$link}'\" {$disabled}>{$name}</button>";
}
?>
@section("body_class")
    common-body
@endsection
@section("body")
<div class="window" id="sidenav">
    <div class="title-bar">
        <div class="title-bar-text">Ticketing98</div>
    </div>
    <div class="window-body sidenav-body">
        <?php
        button("dashboard", "Dashboard");
        button("projects.list", "Projects");
        button("tickets.list", "Tickets");
        button("profile", "Profile");
        button("settings", "Settings");
        ?>
        <form action="{{route("logout")}}" method="post">
            @csrf
            <input type="submit" value="Logout" />
        </form>

    </div>
</div>
<div class="window" id="main">
    <div class="title-bar">
        <div class="title-bar-text">@yield("title")</div>
    </div>
    <div class="window-body">
        @yield("content")
    </div>
</div>
<div class="window bottom-nav-bar">
    <button class="start-menu-button" onclick="startMenuClick()">
        <img alt="Start Icon" src="{{asset("images/icons/windows-0.png")}}">
        <span><b>Start Menu</b></span>
    </button>
</div>
@endsection
