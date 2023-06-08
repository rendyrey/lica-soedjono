<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>LICA - APP</title>

	<!-- Global stylesheets -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
	<link href="{{asset('limitless_assets/css/icons/icomoon/styles.min.css')}}" rel="stylesheet" type="text/css">
	<link href="{{asset('limitless_assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css">
	<link href="{{asset('limitless_assets/css/bootstrap_limitless.min.css')}}" rel="stylesheet" type="text/css">
	<link href="{{asset('limitless_assets/css/layout.min.css')}}" rel="stylesheet" type="text/css">
	<link href="{{asset('limitless_assets/css/components.min.css')}}" rel="stylesheet" type="text/css">
	<link href="{{asset('limitless_assets/css/colors.min.css')}}" rel="stylesheet" type="text/css">
	<!-- /global stylesheets -->

	<!-- Core JS files -->
	<script src="{{asset('limitless_assets/js/main/jquery.min.j')}}s"></script>
	<script src="{{asset('limitless_assets/js/main/bootstrap.bundle.min.js')}}"></script>
	<script src="{{asset('limitless_assets/js/plugins/loaders/blockui.min.js')}}"></script>
	<!-- /core JS files -->

	<!-- Theme JS files -->
	<script src="{{asset('limitless_assets/js/app.js')}}"></script>
	<!-- /theme JS files -->

    <!-- Sign in JS -->
    <script src="{{asset('limitless_assets/js/plugins/forms/validation/validate.min.js')}}"></script>
    <script src="{{asset('js/auth/sign-in.js')}}"></script>
    <!-- /Sign in JS -->
</head>

<body>

	<!-- Page content -->
	<div class="page-content">

		<!-- Main content -->
		<div class="content-wrapper">

			<!-- Content area -->
			<div class="content d-flex justify-content-center align-items-center">

				<!-- Login form -->
                    {!! Form::open(['route' => 'login', 'method' => 'POST', 'class' => 'login-form form-validate-jquery']) !!}
					<div class="card mb-0">
                        @if ($errors->any())
                            <div class="alert alert-danger border-0 alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                                @foreach($errors->all() as $error)
                                    <li><span class="font-weight-semibold">{{ $error }}<span></li>
                                @endforeach
                            </div>
                        @endif

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
						<div class="card-body">
							<div class="text-center mb-3">
								<i class="icon-reading icon-2x text-slate-300 border-slate-300 border-3 rounded-round p-3 mb-3 mt-1"></i>
								<h5 class="mb-0">Login to your account</h5>
								<span class="d-block text-muted">Enter your credentials below</span>
							</div>

							<div class="form-group form-group-feedback form-group-feedback-left">
                                {{ Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'Email or Username']) }}
								<div class="form-control-feedback">
									<i class="icon-user text-muted"></i>
								</div>
							</div>

							<div class="form-group form-group-feedback form-group-feedback-left">
                                {{ Form::password('password', ['class' => 'form-control', 'placeholder' => 'Password'])}}
								<div class="form-control-feedback">
									<i class="icon-lock2 text-muted"></i>
								</div>
							</div>

							<div class="form-group">
								<button type="submit" class="btn btn-primary btn-block">Sign in <i class="icon-circle-right2 ml-2"></i></button>
							</div>

							
                            @if (Route::has('password.request'))
                                <div class="text-center">
                                    <a href="{{ route('password.request') }}" class="link-primary fs-6 fw-bolder">Forgot password?</a>
                                </div>
                            @endif
                        </div>
					</div>
                    {!! Form::close() !!}
				<!-- /login form -->

			</div>
			<!-- /content area -->


			<!-- Footer -->
			<div class="navbar navbar-expand-lg navbar-light">
				<div class="text-center d-lg-none w-100">
					<button type="button" class="navbar-toggler dropdown-toggle" data-toggle="collapse" data-target="#navbar-footer">
						<i class="icon-unfold mr-2"></i>
						Footer
					</button>
				</div>

				<div class="navbar-collapse collapse" id="navbar-footer">
					<span class="navbar-text">
						&copy; {{ date('Y') }}. <a href="#">LICA</a> by Teams
					</span>
				</div>
			</div>
			<!-- /footer -->

		</div>
		<!-- /main content -->

	</div>
	<!-- /page content -->

</body>
</html>
