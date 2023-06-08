<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->

<head>
	<base href="{{ url('/') }}">
	<title>LICA - Login</title>
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
	<link rel="shortcut icon" href="{{asset('metronic_assets/media/logos/favicon-lica2.png')}}" />
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
		<!--begin::Authentication - Sign-in -->
		<div class="d-flex flex-column flex-column-fluid bgi-position-y-bottom position-x-center bgi-no-repeat bgi-size-contain bgi-attachment-fixed" style="background-image: url('{{url('metronic_assets/media/illustrations/sketchy-1/14.png')}}')">
			<!--begin::Content-->
			<div class="d-flex flex-center flex-column flex-column-fluid p-10 pb-lg-20">
				<!--begin::Wrapper-->
				<div class="d-flex flex-center p-10; w-lg-400px">
					<table width="100%">
						<tr>
							<td style="width:50%;"><img src="{{asset('images/logo-diponegoro.png')}}" style="height: 100px; width: 100px; margin-left: 50px;"></td>
							<td style="width:50%;"><img src="{{asset('images/logo-lica.png')}}" style="height: 100px; width: 150px; margin-left: 0px"></td>
						</tr>
					</table>
				</div>
				<br>
				<div class="w-lg-500px bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">
					@if (Session::get('error'))
					<!--begin::Alert-->
					<div class="alert alert-danger">
						<!--begin::Wrapper-->
						<div class="d-flex flex-column">
							<!--begin::Title-->
							<h4 class="mb-1 text-danger">Credential Is Invalid</h4>
							<!--end::Title-->
							<!--begin::Content-->
							<span>{{ Session::get('error') }}</span>
							<!--end::Content-->
						</div>
						<!--end::Wrapper-->
					</div>
					<!--end::Alert-->
					@endif
					<!--begin::Form-->
					{{-- <form class="form w-100" novalidate="novalidate" id="kt_sign_in_form" data-kt-redirect-url="../../demo1/dist/index.html" action="#"> --}}
					{!! Form::open(['route' => 'login', 'method' => 'POST', 'class' => 'login-form form-validate-jquery']) !!}
					<!--begin::Heading-->
					<div class="text-center mb-10">
						<!--begin::Title-->
						<h1 class="text-dark mb-3">Sign In to LICA</h1>
						<!--end::Title-->
						<!--begin::Link-->
						<div class="text-gray-400 fw-bold fs-4">New Here?
							<a href="{{ url('register') }}" class="link-primary fw-bolder">Create an Account</a>
						</div>
						<!--end::Link-->
					</div>
					<!--begin::Heading-->
					<!--begin::Input group-->
					<div class="fv-row mb-5">
						<!--begin::Label-->
						<label class="form-label fs-6 fw-bolder text-dark">Username</label>
						<!--end::Label-->
						<!--begin::Input-->
						{{ Form::text('email', null, ['class' => 'form-control form-control-md form-control-solid']) }}
						<!--end::Input-->
					</div>
					<!--end::Input group-->
					<!--begin::Input group-->
					<div class="fv-row mb-5">
						<!--begin::Wrapper-->
						<div class="d-flex flex-stack mb-2">
							<!--begin::Label-->
							<label class="form-label fw-bolder text-dark fs-6 mb-0">Password</label>
							<!--end::Label-->

						</div>
						<!--end::Wrapper-->
						<!--begin::Input-->
						{{ Form::password('password', ['class' => 'form-control form-control-md form-control-solid'])}}
						<!--end::Input-->
					</div>

					<div class="fv-row mb-5">
						<!--begin::Wrapper-->
						<div class="d-flex flex-stack mb-2">
							<!--begin::Label-->
							<label class="form-label fw-bolder text-dark fs-6 mb-0">Area</label>
							<!--end::Label-->

						</div>
						<!--end::Wrapper-->
						<!--begin::Input-->
						<select name="area_id" id="area_id" class="form-select form-select-solid select-two">
							<option value="central">Central</option>
							<option value="igd">IGD</option>
						</select>
						<!--end::Input-->
					</div>

					<!--end::Input group-->
					<!--begin::Actions-->
					<div class="text-center">
						<!--begin::Submit button-->
						<button type="submit" class="btn btn-md btn-primary w-100 mb-5">
							<span class="indicator-label">Login</span>
							<span class="indicator-progress">Please wait...
								<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
						</button>
						<!--end::Submit button-->
					</div>
					<!--end::Actions-->

					<!--begin::Link-->
					@if (Route::has('password.request'))
					<a href="{{ route('password.request') }}" class="link-primary fs-6 fw-bolder">Forgot Password ?</a>
					@endif
					<!--end::Link-->
					{!! Form::close() !!}
					<!--end::Form-->
				</div>
				<!--end::Wrapper-->
			</div>
			<!--end::Content-->
		</div>
		<!--end::Authentication - Sign-in-->
	</div>
	<!--end::Root-->
	<!--end::Main-->
	<!--begin::Javascript-->
	<script>
		var hostUrl = "assets/";
	</script>
	<!--begin::Global Javascript Bundle(used by all pages)-->
	<script src="{{asset('metronic_assets/plugins/global/plugins.bundle.js')}}"></script>
	<script src="{{asset('metronic_assets/js/scripts.bundle.js')}}"></script>
	<!--end::Global Javascript Bundle-->
	<!--begin::Page Custom Javascript(used by this page)-->
	{{-- <script src="{{asset('metronic_assets/js/custom/authentication/sign-in/general.js')}}"></script> --}}
	<!--end::Page Custom Javascript-->
	<!--end::Javascript-->

	<script src="{{asset('limitless_assets/js/plugins/forms/validation/validate.min.js')}}"></script>
	<script src="{{asset('js/auth/sign-in.js')}}"></script>
</body>
<!--end::Body-->

</html>