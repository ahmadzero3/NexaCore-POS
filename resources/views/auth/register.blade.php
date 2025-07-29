@extends('layouts.guest')
@section('title', __('auth.register'))

@section('container')
<body class="">
	<!--wrapper-->
	<div class="wrapper">
		<div class="section-authentication-cover">
			<div class="">
				<div class="row g-0">

					<div class="col-12 col-xl-7 col-xxl-8 auth-cover-left align-items-center justify-content-center d-none d-xl-flex">

                        <div class="card shadow-none bg-transparent shadow-none rounded-0 mb-0">
							<div class="card-body">
                                 <img src="assets/images/login-images/register-cover.svg" class="img-fluid " width="550" alt=""/>
							</div>
						</div>
						
					</div>

					<div class="col-12 col-xl-5 col-xxl-4 auth-cover-right align-items-center justify-content-center">
						<div class="card rounded-0 m-3 shadow-none bg-transparent mb-0">
							<div class="card-body p-sm-5">
								<div class="">
									<div class="mb-3 text-center">
										<img src={{ "/app/getimage/" . app('site')['colored_logo'] }} width="60" alt="">
									</div>
									<div class="text-center mb-4">
										<h5 class="">{{ app('company')['name'] }}</h5>
										<p class="mb-0">{{ __('auth.fill_details') }}</p>
									</div>
									<div class="form-body">
										<form class="row g-3" method="post" id="registerForm">
											{{-- CSRF Protection --}}
                        					@csrf
                        					<div class="col-12">
												<x-label for="first_name" name="{{ __('user.first_name') }}"/>
												<x-input placeholder="Enter First Name" name="first_name" type='text' :required="true"/>
											</div>
											<div class="col-12">
												<x-label for="last_name" name="{{ __('user.last_name') }}"/>
												<x-input placeholder="Enter Last Name" name="last_name" type='text'/>
											</div>
											<div class="col-12">
												<x-label for="username" name="{{ __('user.username') }}"/>
												<x-input placeholder="Enter Username" name="username" type='text'/>
											</div>
											<div class="col-12">
												<x-label for="email" name="{{ __('user.email') }}"/>
												<x-input placeholder="Enter Email" name="email" type='email' :required="true"/>
											</div>
											<div class="col-12">
												<x-label for="password" name="{{ __('user.password') }}"/>
												<div class="input-group" id="show_hide_password">
													<x-input placeholder="Enter Password" name="password" type='password' :required="true"/>
													<a href="javascript:;" class="input-group-text bg-transparent"><i class="bx bx-hide"></i></a>

												</div>
											</div>
											<div class="col-12">
												<x-label for="password_confirmation" name="{{ __('user.confirm_password') }}"/>
												<div class="input-group" id="show_hide_confirm_password">
													<x-input placeholder="Confirm Password" name="password_confirmation" type='password' :required="true"/>
													<a href="javascript:;" class="input-group-text bg-transparent"><i class="bx bx-hide"></i></a>

												</div>
											</div>
											<div class="col-md-12">
												<x-radio-block id="agree" boxName="agree" text="I read and agree to Terms & Conditions" parentDivClass='form-switch'/>
											</div>
											<div class="col-12">
												<div class="d-grid">
													<x-button class="primary" type="submit" text="Sign up" />
												</div>
											</div>
											<div class="col-12">
												<div class="text-center ">
													<p class="mb-0">Already have an account?
														<x-anchor-tag href="{{ route('login') }}" text="Sign in here" />
													</p>
												</div>
											</div>
										</form>
									</div>

								</div>
							</div>
						</div>
					</div>

				</div>
				<!--end row-->
			</div>
		</div>
	</div>
	<!--end wrapper-->
	
	@section('js')
	<!-- Login page -->
	<script src="custom/js/register.js"></script>
	
	@endsection

</body>
@endsection