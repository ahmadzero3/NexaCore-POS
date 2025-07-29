@php
    function renderOptions($group, $indent = 0) {
        echo '<option value="' . $group->id . '">' . str_repeat('&nbsp;', $indent * 4) . $group->name . '</option>';
        if ($group->children->isNotEmpty()) {
            foreach ($group->children as $child) {
                renderOptions($child, $indent + 1);
            }
        }
    }
    @endphp