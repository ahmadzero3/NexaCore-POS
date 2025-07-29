@if(app('company')['show_terms_and_conditions_on_invoice'])
        <table class="addresses">
            <tr>
                <td class="address">
                    <span class="fw-bold cu-fs-18">{{ __('app.terms_and_conditions') }}</span><br>
                    <span class="cu-fs-14 ">{!! nl2br(app('company')['terms_and_conditions']) !!}</span>
                </td>
            </tr>
        </table>
        @endif