@extends("layout.main")
@section("title")
    Tickets
@endsection

@section("content")
    <h4>Tickets</h4>
    <x-ticket-list :default-status="request()->query('status') ?? 'all'"/>
    @if( $canCreate )
        <button onclick="location.href = '{{ route("tickets.create") }}'">Create new ticket</button>
    @endif
@endsection

