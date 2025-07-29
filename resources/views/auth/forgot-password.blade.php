@extends('layouts.guest')
@section('title', __('auth.forgot_password'))

@section('container')

	<!--wrapper-->
	<div class="wrapper">
		<div class="section-authentication-cover">
			<div class="">
				<div class="row g-0">
					<div class="col-12 col-xl-7 col-xxl-8 auth-cover-left align-items-center justify-content-center d-none d-xl-flex">
                        <div class="card shadow-none bg-transparent shadow-none rounded-0 mb-0">
							<div class="card-body">
                                 <img src="assets/images/login-images/forgot-password-cover.svg" class="img-fluid" width="600" alt=""/>
							</div>
						</div>
					</div>

					<div class="col-12 col-xl-5 col-xxl-4 auth-cover-right align-items-center justify-content-center">
						<div class="card rounded-0 m-3 shadow-none bg-transparent mb-0">
							<div class="card-body p-sm-5">
								
								@include('layouts.session')

								<form class="row g-3" method="POST" action="{{ route('password.email') }}">
								{{-- CSRF Protection --}}
            					@csrf
            					@method('POST')
								<div class="p-3">
									<div class="text-center">
										<img src="assets/images/icons/forgot-2.png" width="100" alt="" />
									</div>
									<h4 class="mt-5 font-weight-bold">{{ __('auth.forgot_password') }}</h4>
									<p class="text-muted">{{ __('auth.enter_registered_email') }}</p>
									<div class="my-4">
										<x-label for="email" name="{{ __('app.email') }}"/>
										<x-input placeholder="Enter Email" name="email" id="email" type='email' value="{{ old('email') }}" :required="true" :autofocus="true" />
									</div>
									<div class="d-grid gap-2">
										<x-button type="submit" class="primary" text="{{ __('app.send') }}" />
										<a href="{{ route('login') }}" class="btn btn-light"><i class='bx bx-arrow-back me-1'></i>{{ __('app.back_to_login') }}</a>
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
	</div>
	<!--end wrapper-->

@endsection

@section('js')
<!-- Login page -->
<script src="custom/js/login.js"></script>

@endsection