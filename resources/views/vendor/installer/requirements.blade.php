@extends('vendor.installer.layouts.master')

@section('template_title')
    {{ trans('installer_messages.requirements.templateTitle') }}
@endsection

@section('title')
    <i class="fa fa-list-ul fa-fw" aria-hidden="true"></i>
    {{ trans('installer_messages.requirements.title') }}
@endsection

@section('container')

    @foreach($requirements['requirements'] as $type => $requirement)
        <ul class="list">
            <li class="list__item list__title {{ $phpSupportInfo['supported'] ? 'success' : 'error' }}">
                <strong>{{ strtoupper($type) }}</strong>
                @if($type == 'php')
                    <strong>
                        <small>
                            (Minimum version {{ $phpSupportInfo['minimum'] }} required)
                        </small>
                    </strong>
                    <span class="float-right">
                        <strong>
                            {{ $phpSupportInfo['current'] }}
                        </strong>
                        <i class="fa fa-fw fa-{{ $phpSupportInfo['supported'] ? 'check-circle-o' : 'exclamation-circle' }} row-icon" aria-hidden="true"></i>
                    </span>
                @endif
            </li>
            @foreach($requirements['requirements'][$type] as $extention => $enabled)
                <li class="list__item {{ $enabled ? 'success' : 'error' }}">
                    {{ $extention }}
                    <i class="fa fa-fw fa-{{ $enabled ? 'check-circle-o' : 'exclamation-circle' }} row-icon" aria-hidden="true"></i>
                </li>
            @endforeach

            <li class="list__item {{ (extension_loaded('nd_pdo_mysql') || extension_loaded('pdo_mysql')) ? 'success' : 'error' }}">

                nd_pdo_mysql <b>or</b> pdo_mysql
                <i class="fa fa-fw fa-{{ (extension_loaded('nd_pdo_mysql') || extension_loaded('pdo_mysql')) ? 'check-circle-o' : 'exclamation-circle' }} row-icon" aria-hidden="true"></i>
            </li>
        </ul>
    @endforeach

    @php
    if($phpSupportInfo['supported']){
        if (extension_loaded('nd_pdo_mysql') || extension_loaded('pdo_mysql')) {
            $phpSupportInfo['supported'] = true;
        } else {
            $phpSupportInfo['supported'] = false;
        }
    }



    @endphp
    @if ( ! isset($requirements['errors']) && $phpSupportInfo['supported'] )
        <div class="buttons">
            <a class="button" href="{{ route('LaravelInstaller::permissions') }}">
                {{ trans('installer_messages.requirements.next') }}
                <i class="fa fa-angle-right fa-fw" aria-hidden="true"></i>
            </a>
        </div>
    @endif

@endsection
