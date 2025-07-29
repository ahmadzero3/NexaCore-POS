<select class="form-select single-select-clear-field" name="timezone" data-placeholder="Choose one thing">

    @foreach ($timezones as $group => $zones)

        @php
            $special = ['&nbsp;', '&#160;', '&#43;', '&#8722;'];
            $group = str_replace($special, '', trim($group));
        @endphp
        <optgroup label="{{ $group }}">

        @foreach ($zones as $name => $zone)
            @php
                $zone = str_replace($special, '', trim($zone));
                $name = str_replace($special, '', trim($name));
            @endphp

            <option value="{{ $name }}" {{ $selected == $name ? 'selected' : '' }}>{{ $zone }}</option>
            
        @endforeach
        
        </optgroup>

    @endforeach

</select>