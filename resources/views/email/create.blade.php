@extends('layouts.app')
@section('title', __('message.create_email'))

@section('css')
<link rel="stylesheet" href="{{ versionedAsset('custom/libraries/quil-editor/quill.snow.css') }}">
@endsection

        @section('content')
        <!--start page wrapper -->
        <div class="page-wrapper">
            <div class="page-content">
                <x-breadcrumb :langArray="[
                                            'message.email',
                                            'message.create_email',
                                        ]"/>
                <div class="row">
                    <div class="col-12 col-lg-12">
                        <div class="card">
                            <div class="card-header px-4 py-3">
                                <h5 class="mb-0">{{ __('message.create_email') }}</h5>
                            </div>
                            <div class="card-body p-4">
                                <form class="row g-3 needs-validation" id="emailForm" action="{{ route('email.send') }}" enctype="multipart/form-data">
                                    {{-- CSRF Protection --}}
                                    @csrf
                                    @method('POST')

                                    <div class="col-md-12">
                                        <x-label for="email" name="{{ __('message.email_ids') }}" />
                                        <x-input type="email" name="email" :required="true" value="" placeholder="{{ __('message.comma_separator_for_multiple_email_ids') }}" :autofocus='true'/>
                                    </div>

                                    <div class="col-md-12">
                                        <x-label for="subject" name="{{ __('message.subject') }}" />
                                        <x-input type="text" name="subject" :required="true" value="" placeholder="{{ __('message.subject') }}"/>
                                    </div>

                                    <div class="col-md-12">
                                        <x-label for="message" name="{{ __('message.message') }}" />
                                        <!-- <x-textarea name="message" value=""/> -->
                                        <div id="editor">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <x-label for="message" name="{{ __('message.attachment') }}" />
                                        <div class="input-group mb-3">
                                            <input type="file" id="attachment" name="attachment" class="form-control">
                                            <button id="removeBtn" class="btn btn-outline-secondary btn-remove" disabled>Remove</button>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="d-md-flex d-grid align-items-center gap-3">
                                            <x-button type="submit" class="primary px-4" text="{{ __('message.send') }}" />
                                            <x-anchor-tag href="{{ route('dashboard') }}" text="{{ __('app.close') }}" class="btn btn-light px-4" />
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end row-->
            </div>
        </div>
        @endsection

@section('js')
<script src="{{ versionedAsset('custom/js/email/email.js') }}"></script>
<script src="{{ versionedAsset('custom/libraries/quil-editor/quill.js') }}"></script>
@endsection
