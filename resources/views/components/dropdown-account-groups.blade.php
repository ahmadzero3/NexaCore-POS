<select class="form-select single-select-field" name="{{ $idName }}" id="{{ $idName }}" data-placeholder="Choose one thing">
    
    @if($showMain)
        <option value="0">Main</option>
    @endif

    @php
    function renderOptions($group, $indent = 0, $selected) {
        $showArrow = ($indent!=0) ? ' &#8627; ' : '';
        $selectedId = ($selected == $group->id) ? 'selected' : '';
        echo '<option value="' . $group->id . '" '.$selectedId.'>' . str_repeat('&nbsp;', $indent * 4) . $showArrow . $group->name . '</option>';
        if ($group->children->isNotEmpty()) {
            foreach ($group->children as $child) {
                renderOptions($child, $indent + 1, $selected);
            }
        }
    }
    foreach ($groups as $group){
        renderOptions($group, 0, $selected);
    }
    @endphp
</select>
