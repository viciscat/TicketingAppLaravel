@extends("layout.main")
@section("title")
    Log Wizard
@endsection

@section("content")
    <h4 style="flex-shrink: 0">Log Wizard</h4>
    <div class="window-content">
        <p>
            The Log Wizard will help you record how much time you spent on a ticket. <br/>
            This wizard is for <a href="{{ route("tickets.view", $ticket->id) }}">{{ $ticket->title }}</a>
        </p>
        <form id="create-log-form" method="post" class="basic-form" action="{{ route("tickets.log.store", $ticket->id) }}">
            @csrf
            <input type="hidden" id="time-spent" name="time-spent" value="{{ old("time-spent") }}"/>
            <div class="field-row-stacked">
                <label for="start">Entry Start</label>
                <input type="datetime-local" id="start" value="{{ old("start") }}" name="start" oninput="markStartChanged()"/>
                <x-input-error for="start"/>
            </div>
            <div class="field-row-stacked">
                <label for="time-spent">Time Spent</label>
                <div class="input-horizontal-container" id="time-spent-container">
                    <input type="text" id="time-spent-fake" oninput="parseDuration()" />
                    <span></span>
                </div>
                <x-input-error for="time-spent"/>
            </div>
            <div class="field-row-stacked" style="width: 300px">
                <label for="comment">Description</label>
                <textarea id="comment" rows="3" name="comment">{{ old("comment") }}</textarea>
            </div>
        </form>
    </div>
    <input type="submit" form="create-log-form" value="Submit">
@endsection
@section("inline-script")
    <script>
        const timeSpentInput = document.getElementById("time-spent-fake");
        const startTimeInput = document.getElementById("start");
        const timeSpentPreview = document.querySelector("#time-spent-container span")
        const timeSpentData = document.getElementById("time-spent");

        let startChangedManually = false;

        function markStartChanged() {
            startChangedManually = true;
        }

        function pad(num) {
            return (num < 10 ? '0' + num : num);
        }

        const timeUnits = {
            'd': 60 * 24, // hopefully no one ever needs to use that, but here nonetheless
            'h': 60,
            'm': 1
        }
        function parseDuration() {
            let minutes = 0;
            for (let timeUnit in timeUnits) {
                const regex = new RegExp("(\\d+)\\s*" + timeUnit, "i");
                const match = timeSpentInput.value.match(regex);
                if (match) {
                    minutes += parseInt(match[1], 10) * timeUnits[timeUnit];
                }
            }
            timeSpentData.value = Math.trunc(minutes); // probably useless but floats are scawy
            if (!startChangedManually) {
                let date = new Date();
                date.setTime(date.getTime() - minutes * 60 * 1000)
                startTimeInput.value = `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`; // why is there no easier way
            }

            let preview = "";
            for (let timeUnit in timeUnits) {
                let t = Math.trunc(minutes / timeUnits[timeUnit]);
                preview += t + timeUnit + " ";
                minutes -= t * timeUnits[timeUnit];
            }

            timeSpentPreview.innerHTML = preview;
        }
    </script>
@endsection
