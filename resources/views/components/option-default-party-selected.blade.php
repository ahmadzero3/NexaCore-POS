@if($defaultParty)
    <option value="{{ $defaultParty->id }}">{{ $defaultParty->first_name . ' ' . $defaultParty->last_name }}</option>
@endif