<div class="modal fade" id="modalDeleteFuelStation-{{ $fuelStation->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel1">{{ __('Delete Fuel Receipt') }} | {{ $fuelStation->vehicle->name }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('fuel-stations.destroy',  $fuelStation->id) }}" method="POST">
        @csrf
        @method('DELETE')
        <div class="modal-body">
          {{ __('Do you really want to delete this fuel receipt?') }} {{ $fuelStation->vehicle->name }}
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
          <button type="submit" class="btn btn-outline-danger">{{ __('Delete') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>


