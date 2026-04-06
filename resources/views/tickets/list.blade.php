@extends("layout.main")
@section("title")
    Tickets
@endsection

<script src="{{ asset("js/ticketListFilter.js") }}" defer></script>
@section("content")
    <h4>Tickets</h4>
    <x-ticket-list :default-status="request()->get('status') ?? 'All'"/>
    <a href="{{ route("tickets.create") }}">
        <button>Create new ticket</button>
    </a>
@endsection

