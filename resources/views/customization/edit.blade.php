@extends('layouts.app')
@section('title', __('Customization'))

@section('content')
<div class="page-wrapper">
    <div class="page-content">

        <x-breadcrumb :langArray="[
                                            'app.settings',
                                            'Customization',
                                        ]" />
        <div class="row">
            <div class="container">
                <div class="card">
                    <div class="card-header px-4 py-3">
                        <h5 class="mb-0">{{ __('Customization') }}</h5>
                    </div>
                    <div class="card-body">
                        <form class="row g-3 needs-validation" action="{{ route('customize.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            {{-- two-column row for Header & Heading --}}
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label class="py-3" for="color">Card Header Color</label>
                                        <input type="color" id="color" name="color" value="{{ $color }}" class="form-control" style="width: 100px;">
                                    </div>
                                    <div class="form-group">
                                        <label class="py-3" for="border_color">Card Border Color</label>
                                        <input type="color" id="border_color" name="border_color" value="{{ $borderColor }}" class="form-control" style="width: 100px;">
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label class="py-3" for="heading_color">Heading Color</label>
                                        <input type="color" id="heading_color" name="heading_color" value="{{ $headingColor }}" class="form-control" style="width: 100px;">
                                    </div>

                                    <div class="form-group mt-3">
                                        <label class="py-3" for="toggle_switch">{{ __('Trending Items') }}</label>
                                        <div class="form-check form-switch">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                id="toggle_switch"
                                                name="toggle_switch"
                                                style="transform: scale(1.7); margin-left: -22px;"
                                                {{ old('toggle_switch', $toggle_switch === 'active' ? true : false) ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection