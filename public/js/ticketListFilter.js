function updateFilters() {
    const ticketType = document.getElementById('ticket-type-filter').value.toLowerCase();
    const ticketStatus = document.getElementById('ticket-status-filter').value.toLowerCase();

    const url = new URL(location.href);
    url.searchParams.set('filter-type', ticketType);
    url.searchParams.set('filter-status', ticketStatus);
    window.location.replace(url.toString());
}

function setFilterIfValid(filterId, value) {
    const ticketType = document.getElementById(filterId);
    let find = ticketType.querySelectorAll("option").values().find((item) => item.value === value);
    console.log(filterId, find);
    if (find !== undefined) {
        ticketType.value = find.value;
    } else {
        ticketType.value = "all";
        return false;
    }
    return true;
}

document.addEventListener('DOMContentLoaded', () => {
    const url = new URL(location.href);
    let valid = true;
    valid &= setFilterIfValid("ticket-type-filter", url.searchParams.get('filter-type'));
    valid &= setFilterIfValid("ticket-status-filter", url.searchParams.get('filter-status'));
    if (!valid) updateFilters();
});