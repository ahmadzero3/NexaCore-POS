@if (!$justLinks)
    <li class="nav-item dropdown dropdown-laungauge d-none d-sm-flex">
        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="avascript:;" data-bs-toggle="dropdown">
            <span>{{ $currentLangData }}</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
            @foreach ($languages as $language)
                <li>
                    <a class="dropdown-item d-flex align-items-center py-2"
                        href="{{ route('language.switch', ['id' => $language->id]) }}">
                        <span class="ms-2">{{ strtoupper($language->code) }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </li>
@elseif($justLinks)
    @foreach ($languages as $language)
        <x-anchor-tag href="{{ route('language.switch', ['id' => $language->id]) }}" text="{{ $language->code }}" />
    @endforeach
@endif
