<!-- Modal 1-->
<div class="modal fade" id="modalDeleteBuilding{{ $category->id }}" aria-labelledby="modalToggleLabel" tabindex="-1" style="display: none;" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalToggleLabel">{{ __('Delete building material') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('building-materals.destroy', $category->id) }}" method="POST">
        @csrf
        @method('DELETE')
        <div class="modal-body">
          {{ __('Do you really want to delete this material?') }}
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-outline-danger" data-bs-target="#modalToggle2" data-bs-toggle="modal" data-bs-dismiss="modal">{{ __('Delete') }}</button>
        </div>
      <form>
    </div>
  </div>
</div>
