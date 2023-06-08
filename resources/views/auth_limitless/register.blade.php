<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Limitless - Responsive Web Application Kit by Eugene Kopyov</title>

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
	<script src="{{asset('limitless_assets/js/main/jquery.min.js')}}"></script>
	<script src="{{asset('limitless_assets/js/main/bootstrap.bundle.min.js')}}"></script>
	<script src="{{asset('limitless_assets/js/plugins/loaders/blockui.min.js')}}"></script>
	<!-- /core JS files -->

	<!-- Theme JS files -->
	<script src="{{asset('limitless_assets/js/plugins/forms/styling/uniform.min.js')}}"></script>

	<script src="{{asset('limitless_assets/js/app.js')}}"></script>
	{{-- <script src="{{asset('limitless_assets/js/demo_pages/login.js')}}"></script> --}}

    <!-- sign up js -->
    <script src="{{asset('limitless_assets/js/plugins/forms/validation/validate.min.js')}}"></script>
    <script src="{{asset('js/auth/sign-up.js')}}"></script>
    <!-- /sign up js -->
	<!-- /theme JS files -->

</head>

<body>
	<!-- Page content -->
	<div class="page-content">

		<!-- Main content -->
		<div class="content-wrapper">

			<!-- Content area -->
			<div class="content d-flex justify-content-center align-items-center">
               
				<!-- Registration form -->
				{{-- <form class="login-form" action="index.html"> --}}
                {!! Form::open(['route' => 'register', 'method' => 'POST', 'class' => 'login-form form-validate-jquery']) !!}
					<div class="card mb-0">
                        @if ($errors->any())
                        <div class="alert alert-danger border-0 alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                            @foreach($errors->all() as $error)
                                <li><span class="font-weight-semibold">{{ $error }}<span></li>
                            @endforeach
                        </div>
                          
                        @endif
						<div class="card-body">
							<div class="text-center mb-3">
								<h3 class="mb-0">Create account</h3>
								<span class="d-block text-muted">All fields are required</span>
							</div>

							<div class="form-group text-center text-muted content-divider">
								<span class="px-2">Your credentials</span>
							</div>

                            <div class="form-group form-group-feedback form-group-feedback-left">
                                {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Name'])}}
								<div class="form-control-feedback">
									<i class="icon-user text-muted"></i>
								</div>
							</div>

							<div class="form-group form-group-feedback form-group-feedback-left">
                                {{ Form::text('username', null, ['class' => 'form-control', 'placeholder' => 'Username'])}}
								<div class="form-control-feedback">
									<i class="icon-user-check text-muted"></i>
								</div>
							</div>

                            <div class="form-group form-group-feedback form-group-feedback-left">
                                {{ Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'Email'])}}
								<div class="form-control-feedback">
									<i class="icon-mention text-muted"></i>
								</div>
							</div>

							<div class="form-group form-group-feedback form-group-feedback-left">
                                {{ Form::password('password', ['class' => 'form-control', 'placeholder' => 'Password', 'id' => 'password'])}}
								<div class="form-control-feedback">
									<i class="icon-user-lock text-muted"></i>
								</div>
							</div>
                            
							<div class="form-group form-group-feedback form-group-feedback-left">
                                {{ Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => 'Confirmation Password'])}}
								<div class="form-control-feedback">
									<i class="icon-user-lock text-muted"></i>
								</div>
							</div>

							<button type="submit" class="btn bg-teal-400 btn-block">Register <i class="icon-circle-right2 ml-2"></i></button>
						</div>
					</div>
				{!! Form::close() !!}
				<!-- /registration form -->

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

	<script>
		var base = "{{ url('/') }}";
	</script>
</body>
</html>
