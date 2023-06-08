<!DOCTYPE html>
<!--
Author: Keenthemes
Product Name: Metronic - Bootstrap 5 HTML, VueJS, React, Angular & Laravel Admin Dashboard Theme
Purchase: https://1.envato.market/EA4JP
Website: http://www.keenthemes.com
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Dribbble: www.dribbble.com/keenthemes
Like: www.facebook.com/keenthemes
License: For each use you must have a valid license purchased only from above link in order to legally use the theme for your project.
-->
<html lang="en">
<!--begin::Head-->

<head>
	<base href="{{url('/')}}">
	<title>LICA - Register</title>
	<meta charset="utf-8" />
	<meta name="description" content="The most advanced Bootstrap Admin Theme on Themeforest trusted by 94,000 beginners and professionals. Multi-demo, Dark Mode, RTL support and complete React, Angular, Vue &amp; Laravel versions. Grab your copy now and get life-time updates for free." />
	<meta name="keywords" content="Metronic, bootstrap, bootstrap 5, Angular, VueJs, React, Laravel, admin themes, web design, figma, web development, free templates, free admin themes, bootstrap theme, bootstrap template, bootstrap dashboard, bootstrap dak mode, bootstrap button, bootstrap datepicker, bootstrap timepicker, fullcalendar, datatables, flaticon" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta property="og:locale" content="en_US" />
	<meta property="og:type" content="article" />
	<meta property="og:title" content="Metronic - Bootstrap 5 HTML, VueJS, React, Angular &amp; Laravel Admin Dashboard Theme" />
	<meta property="og:url" content="https://keenthemes.com/metronic" />
	<meta property="og:site_name" content="Keenthemes | Metronic" />
	<link rel="canonical" href="https://preview.keenthemes.com/metronic8" />
	<link rel="shortcut icon" href="metronic_assets/media/logos/favicon.ico" />
	<!--begin::Fonts-->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600,700" />
	<!--end::Fonts-->
	<!--begin::Global Stylesheets Bundle(used by all pages)-->
	<link href="{{asset('metronic_assets/plugins/global/plugins.bundle.css')}}" rel="stylesheet" type="text/css" />
	<link href="{{asset('metronic_assets/css/style.bundle.css')}}" rel="stylesheet" type="text/css" />
	<!--end::Global Stylesheets Bundle-->
</head>
<!--end::Head-->
<!--begin::Body-->

<body id="kt_body" class="bg-body">
	<!--begin::Main-->
	<!--begin::Root-->
	<div class="d-flex flex-column flex-root">
		<!--begin::Authentication - Sign-up -->
		<div class="d-flex flex-column flex-column-fluid bgi-position-y-bottom position-x-center bgi-no-repeat bgi-size-contain bgi-attachment-fixed" style="background-image: url(metronic_assets/media/illustrations/sketchy-1/14.png">
			<!--begin::Content-->
			<div class="d-flex flex-center flex-column flex-column-fluid p-10 pb-lg-20">
				<!--begin::Wrapper-->
				<div class="w-lg-600px bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">
					<!--begin::Form-->
					{!! Form::open(['route' => 'register', 'method' => 'POST', 'class' => 'login-form form-validate-jquery']) !!}
					<!--begin::Heading-->
					<div class="mb-10 text-center">
						<!--begin::Title-->
						<h1 class="text-dark mb-3">Create an Account</h1>
						<!--end::Title-->
						<!--begin::Link-->
						<div class="text-gray-400 fw-bold fs-4">Already have an account?
							<a href="{{ url('login') }}" class="link-primary fw-bolder">Sign in here</a>
						</div>
						<!--end::Link-->
					</div>
					<!--end::Heading-->
					<!--begin::Input group-->
					<div class="row fv-row mb-7">
						<!--begin::Col-->
						<div class="col">
							<label class="form-label fw-bolder text-dark fs-6">Name</label>
							{{ Form::text('name', null, ['class' => 'form-control form-control-md form-control-solid'])}}
						</div>
						<!--end::Col-->
					</div>
					<!--end::Input group-->
					<!--begin::Input group-->
					<div class="row fv-row mb-7">
						<!--begin::Col-->
						<div class="col">
							<label class="form-label fw-bolder text-dark fs-6">Username</label>
							{{ Form::text('username', null, ['class' => 'form-control form-control-md form-control-solid'])}}
						</div>
						<!--end::Col-->
					</div>
					<!--end::Input group-->
					<!--begin::Input group-->
					<div class="fv-row mb-7">
						<label class="form-label fw-bolder text-dark fs-6">Email</label>
						{{ Form::text('email', null, ['class' => 'form-control form-control-md form-control-solid'])}}
					</div>
					<!--end::Input group-->
					<!--begin::Input group-->
					<div class="mb-10 fv-row" data-kt-password-meter="true">
						<!--begin::Wrapper-->
						<div class="mb-1">
							<!--begin::Label-->
							<label class="form-label fw-bolder text-dark fs-6">Password</label>
							<!--end::Label-->
							<!--begin::Input wrapper-->
							<div class="position-relative mb-3">
								{{ Form::password('password', ['class' => 'form-control form-control-md form-control-solid', 'id' => 'password'])}}
							</div>
							<!--end::Input wrapper-->
						</div>
						<!--end::Wrapper-->
					</div>
					<!--end::Input group=-->
					<!--begin::Input group-->
					<div class="fv-row mb-5">
						<label class="form-label fw-bolder text-dark fs-6">Confirm Password</label>
						{{ Form::password('password_confirmation', ['class' => 'form-control form-control-md form-control-solid'])}}
					</div>
					<!--end::Input group-->
					<!--begin::Actions-->
					<div class="text-center">
						<button type="submit" id="kt_sign_up_submit" class="btn btn-lg btn-primary">
							<span class="indicator-label">Submit</span>
							<span class="indicator-progress">Please wait...
								<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
						</button>
					</div>
					<!--end::Actions-->
					</form>
					<!--end::Form-->
				</div>
				<!--end::Wrapper-->
			</div>
			<!--end::Content-->
			<!--begin::Footer-->
			<div class="d-flex flex-center flex-column-auto p-10">
				<!--begin::Links-->
				<div class="d-flex align-items-center fw-bold fs-6">
					<a href="https://keenthemes.com" class="text-muted text-hover-primary px-2">About</a>
					<a href="mailto:support@keenthemes.com" class="text-muted text-hover-primary px-2">Contact</a>
					<a href="https://1.envato.market/EA4JP" class="text-muted text-hover-primary px-2">Contact Us</a>
				</div>
				<!--end::Links-->
			</div>
			<!--end::Footer-->
		</div>
		<!--end::Authentication - Sign-up-->
	</div>
	<!--end::Root-->
	<!--end::Main-->
	<!--begin::Javascript-->
	<script>
		var hostUrl = "{{asset('metronic_assets/')}}";
	</script>
	<!--begin::Global Javascript Bundle(used by all pages)-->
	<script src="{{asset('metronic_assets/plugins/global/plugins.bundle.js')}}"></script>
	<script src="{{asset('metronic_assets/js/scripts.bundle.js')}}"></script>
	<!--end::Global Javascript Bundle-->
	<!--begin::Page Custom Javascript(used by this page)-->
	{{-- <script src="metronic_assets/js/custom/authentication/sign-up/general.js"></script> --}}
	<script>
		var base = "{{ url('/') }}";
	</script>
	<script src="{{asset('limitless_assets/js/plugins/forms/validation/validate.min.js')}}"></script>
	<script src="{{asset('js/auth/sign-up.js')}}"></script>

	<!--end::Page Custom Javascript-->
	<!--end::Javascript-->
</body>
<!--end::Body-->

</html>