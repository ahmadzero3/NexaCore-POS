<!-- Status History Modal: start -->
<div class="modal fade" id="statusHistoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" >{{ __('app.status_update_history') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class=" needs-validation" id="statusHistoryForm" action="" enctype="multipart/form-data">
                {{-- CSRF Protection --}}
                @csrf
                @method('POST')

                <input type="hidden" id="history_of" value="{{ $history_of }}">
                <div class="modal-body row g-3">
                        <div class="col-md-12">
                            <x-label for="mobile_numbers" name="{{ __('message.mobile_number') }}" />
                            <table class="table table-bordered" id="status-history-table">
                                <thead>
                                    <tr>
                                        <td>#</td>
                                        <td>{{ __('app.date') }}</td>
                                        <td>{{ __('app.status') }}</td>
                                        <td>{{ __('app.created_by') }}</td>
                                        <td>{{ __('app.updated_by') }}</td>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Tax Modal: end -->
