<!-- Tax Modal: start -->
<div class="modal fade" id="expenseCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" >{{ __('expense.category.create') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class=" needs-validation" id="expenseCategoryForm" action="{{ route('expense.category.store') }}" enctype="multipart/form-data">
                {{-- CSRF Protection --}}
                @csrf
                @method('POST')
                <div class="modal-body row g-3">
                        <div class="col-md-6">
                            <x-label for="name" name="{{ __('app.name') }}" />
                            <x-input type="text" name="name" :required="true" value=""/>
                        </div>
                        <div class="col-md-6">
                            <x-label for="expense_category_id" name="{{ __('expense.category.type') }}" />
                            <x-dropdown-account-expense-type selected="" />
                        </div>
                        <div class="col-md-6">
                            <x-label for="description" name="{{ __('app.description') }}" />
                            <x-textarea name="description" value=""/>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <x-button type="submit" class="btn btn-primary" text="{{ __('app.submit') }}" />
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Tax Modal: end -->