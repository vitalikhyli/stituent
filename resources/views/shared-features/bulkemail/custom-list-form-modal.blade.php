
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Custom Bulk Email List</h4>
      </div>
      <div class="modal-body">

        <div class="">
          <input type="text" class="border-2 px-4 py-2 text-grey-dark mb-4 w-1/2" placeholder="List Name" />
        </div>
        
      <ul class="nav nav-pills">
        @foreach ($categories as $category)
          @if ($loop->first)
            <li class="active"><a data-toggle="pill" href="#people-list-category-{{ $category->id }}">
              {{ ucwords($category->name) }}
            </a></li>
          @else
            <li><a data-toggle="pill" href="#people-list-category-{{ $category->id }}">
              {{ ucwords($category->name) }}
            </a></li>
          @endif
        @endforeach
        <li><a data-toggle="pill" href="#people-list-geography">Geography</a></li>
        <li><a data-toggle="pill" href="#people-list-status">Voting Status</a></li>
      </ul>

      <div class="tab-content">
        @foreach ($categories as $category)
          @if ($loop->first)
            <div id="people-list-category-{{ $category->id }}" class="tab-pane p-6 fade in active">
          @else
            <div id="people-list-category-{{ $category->id }}" class="tab-pane p-6 fade">
          @endif
            <h3>{{ ucwords($category->name) }}</h3>
            @foreach ($category->groups()->where('team_id', Auth::user()->team_id)->get() as $group)
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="groups" value="{{ $group->id }}">
                  {{ $group->name }}
                </label>
              </div>
            @endforeach
          </div>
        @endforeach
        <div id="people-list-geography" class="tab-pane p-6 fade">
          <h3>Geography</h3>
          <p>Some content in menu 1.</p>
        </div>
        <div id="people-list-status" class="tab-pane p-6 fade">
          <h3>Voting Status</h3>
          <p>Some content in menu 2.</p>
        </div>
      </div>

      </div>
      <div class="modal-footer">
        
        
        <button type="button" class="btn btn-primary float-right ml-4">Save</button>
        <button type="button" class="btn btn-default float-left ml-4" data-dismiss="modal">Close</button>
        <div class="float-right text-grey-dark text-lg mr-4">
          <b id="people-list-count" class="text-green text-2xl">152</b> People
        </div>
      </div>
    </div>

