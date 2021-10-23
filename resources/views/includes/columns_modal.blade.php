<!-- Modal -->
<div class="modal fade bd-example-modal-sm" id="columnsModal" tabindex="-1" role="dialog" aria-labelledby="columnsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="columnsModalLabel">Filterable columns</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <form action="/sources" id="columns-form" method="post">
                @csrf
                @method('PUT')
                @foreach ($filterable_columns as $column => $column_name)
                    <div class="form-check">
                        <label class="form-check-label" for="{{ $column }}">
                            <input type="checkbox" class="form-check-input toggle-vis" id="{{ $column }}" name="columns[]" value="{{ $column }}" data-column="{{ $loop->index }}" @if ($sources->contains($column)) {{ 'checked' }} @endif>{{ $column_name }}
                        </label>
                    </div>
                @endforeach
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" id="set-filtered-columns" class="btn btn-primary" data-dismiss="modal">Save changes</button>
        </div>
    </div>
  </div>
</div>