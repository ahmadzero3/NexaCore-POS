<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 p-0">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}" class="text-primary">
                    <i class="bx bx-home-alt"></i>
                </a>
            </li>

            @foreach ($langArray as $index => $lang)
            <li class="breadcrumb-item {{ $index === 0 ? 'text-primary' : '' }}" aria-current="page">
                {{ __($lang) }}
            </li>
            @endforeach
        </ol>
    </nav>
</div>
<!--end breadcrumb-->