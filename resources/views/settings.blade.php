@extends("layout.main")
@section("title")
    Settings
@endsection
@section("content")
      <div class="field-row">
        <label for="language">Language</label>
        <select id="language">
          <option value="en">English</option>
        </select>
      </div>
      <div class="field-row">
      <label>Bumpscosity:</label>
      <label for="bumpscosity">Low</label>
      <input id="bumpscosity" type="range" min="1" max="11" value="5" />
      <label>High</label>
      </div>
@endsection
