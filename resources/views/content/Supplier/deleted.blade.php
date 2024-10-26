<div class="modal fade" id="modalDeleteSupplier{{ $supplier->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel1">{{ __('Delete delivery driver') }} | {{ $supplier->fullname }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('supplier.destroy',  $supplier->id) }}" method="POST">
        @csrf
        @method('DELETE')
        <div class="modal-body">
          {{ __('Do you really want to delete this Delivery driver?') }} {{ $supplier->fullname }}
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
          <button type="submit" class="btn btn-outline-danger">{{ __('Delete') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>


