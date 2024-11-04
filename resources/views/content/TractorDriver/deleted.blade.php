<div class="modal fade" id="modalDeleteTractorDriver{{ $tractorDriver->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel1">{{ __('Delete Tractor Driver') }} | {{ $tractorDriver->fullname }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('services.tractor-driver.destroy',  $tractorDriver->id) }}" method="POST">
        @csrf
        @method('DELETE')
        <div class="modal-body">
          {{ __('Do you really want to delete this Delivery driver?') }} {{ $tractorDriver->fullname }}
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
          <button type="submit" class="btn btn-outline-danger">{{ __('Delete') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>


