{{-- resources/views/components/dropdown-sorting-pos.blade.php --}}
@php
$sortingPreference = auth()->user()->pos_sorting_preference ?? 'a_to_z';
@endphp

<div class="dropdown">
    <button class="btn btn-secondary dropdown-toggle" type="button"
        id="sortingDropdown" data-bs-toggle="dropdown">
        <i class="bx bx-sort"></i>
    </button>
    <ul class="dropdown-menu" aria-labelledby="sortingDropdown">
        @foreach(['a_to_z', 'z_to_a', 'latest_product', 'oldest_product', 'manual_sorting'] as $option)
        <li>
            <a class="dropdown-item {{ $sortingPreference === $option ? 'active' : '' }}"
                href="#"
                data-value="{{ $option }}">
                {{ ucwords(str_replace('_', ' ', $option)) }}
            </a>
        </li>
        @endforeach
    </ul>
</div>

{{-- Hidden field for your existing JS to read --}}
<input type="hidden" id="sorting_preference" value="{{ $sortingPreference }}">

<input type="hidden" id="manual_order_input"
    value='@json(auth()->user()->pos_manual_order ?? [])'>


@push('scripts')
<script>
    // Make sure Laravel’s CSRF token is sent with every AJAX request
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Whenever a sorting option is clicked…
    $(document).on('click', '.dropdown-item[data-value]', function(e) {
        e.preventDefault();

        const value = $(this).data('value');
        const label = $(this).text();

        // 1. Update the dropdown’s label
        $('#sortingDropdown').html(`<i class="bx bx-sort"></i> ${label}`);

        // 2. Mark the active item in the menu
        $('.dropdown-item').removeClass('active');
        $(this).addClass('active');

        // 3. Update your hidden input so loadMoreItems() picks it up
        $('#sorting_preference').val(value);

        // 4. Send the new preference to your controller
        $.post("{{ route('pos.saveSortingPreference') }}", {
                sorting_preference: value
            })
            .done(function(res) {
                console.log(res.message);
                // 5. (Optional) reload the grid from page 1
                if (typeof loadMoreItems === 'function') {
                    currentPage = 0;
                    startFromFirst = 0;
                    loadMoreItems();
                }
            })
            .fail(function(xhr) {
                console.error('Could not save sorting preference:', xhr.responseText);
            });
    });
</script>
@endpush