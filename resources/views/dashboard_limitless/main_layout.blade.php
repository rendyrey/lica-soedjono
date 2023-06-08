<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
	<title>LICA Master @if(isset($masterData)) - {{ucwords($masterData) }} @endif</title>

	<!-- Global stylesheets -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
	<link href="{{asset('limitless_assets/css/icons/icomoon/styles.min.css')}}" rel="stylesheet" type="text/css">
	<link href="{{asset('limitless_assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css">
	<link href="{{asset('limitless_assets/css/bootstrap_limitless.min.css')}}" rel="stylesheet" type="text/css">
	<link href="{{asset('limitless_assets/css/layout.min.css')}}" rel="stylesheet" type="text/css">
	<link href="{{asset('limitless_assets/css/components.min.css')}}" rel="stylesheet" type="text/css">
	<link href="{{asset('limitless_assets/css/colors.min.css')}}" rel="stylesheet" type="text/css">
	<!-- /global stylesheets -->

	<!-- font awesome -->
	<link href="{{asset('limitless_assets/css/icons/fontawesome/styles.min.css')}}" rel="stylesheet" type="text/css">
	<!-- /font awesome -->

	<!-- Core JS files -->
	<script src="{{asset('limitless_assets/js/main/jquery.min.js')}}"></script>
	<script src="{{asset('limitless_assets/js/main/bootstrap.bundle.min.js')}}"></script>
	<script src="{{asset('limitless_assets/js/plugins/loaders/blockui.min.js')}}"></script>
	<script src="{{asset('limitless_assets/js/plugins/ui/slinky.min.js')}}"></script>
	<!-- /core JS files -->

	<!-- Theme JS files -->
	<script src="{{asset('limitless_assets/js/app.js')}}"></script>
	<!-- /theme JS files -->

</head>

<body>

	<!-- Main navbar -->
	<div class="navbar navbar-expand-md navbar-dark fixed-top">
		<div class="navbar-brand wmin-0 mr-5">
			<a href="index.html" class="d-inline-block">
				<img src="{{asset('limitless_assets/images/logo_light.png')}}" alt="">
			</a>
		</div>

		<div class="d-md-none">
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-mobile">
				<i class="icon-tree5"></i>
			</button>
		</div>

		<div class="collapse navbar-collapse" id="navbar-mobile">
			<ul class="navbar-nav">
				<li class="nav-item dropdown">
					<a href="{{ url('pre-analytics') }}" class="navbar-nav-link dropdown-toggle caret-0">
                        <i class="icon-file-plus"></i>
						Pre Analytics
						<span class="badge badge-primary badge-pill" id="pre-analytics-badge"></span>
					</a>
				</li>

				<li class="nav-item dropdown">
					<a href="#" class="navbar-nav-link dropdown-toggle caret-0">
						<i class="icon-pulse2"></i>
                        Analytics
						<span class="badge badge-warning badge-pill" id="analytics-badge"></span>
					</a>
				</li>

                <li class="nav-item dropdown">
					<a href="#" class="navbar-nav-link dropdown-toggle caret-0">
						<i class="icon-file-check"></i>
                        Post Analytics
						<span class="badge badge-success badge-pill" id="post-analytics-badge"></span>
					</a>
				</li>

                <li class="nav-item dropdown">
					<a href="#" class="navbar-nav-link dropdown-toggle" data-toggle="dropdown">
						<i class="icon-database-menu"></i>
                        Masters
					</a>

                    <div class="dropdown-menu">
						<div class="dropdown-header">Basic layouts</div>
						@foreach(Helper::masterMenu() as $url => $urlTitle)
                        	<a href="{{ $url }}" class="dropdown-item"> {{ $urlTitle }}</a>
						@endforeach
					</div>
                    
				</li>
			</ul>

            <span class="mr-md-auto"></span>

			<ul class="navbar-nav">
				<li class="nav-item dropdown dropdown-user">
					<a href="#" class="navbar-nav-link d-flex align-items-center dropdown-toggle" data-toggle="dropdown">
						<img src="{{asset('limitless_assets/images/placeholders/placeholder.jpg')}}" class="rounded-circle mr-2" height="34" alt="">
						<span>Victoria</span>
					</a>

					<div class="dropdown-menu dropdown-menu-right">
						<a href="#" class="dropdown-item"><i class="icon-user-plus"></i> My profile</a>
						<a href="#" class="dropdown-item"><i class="icon-coins"></i> My balance</a>
						<a href="#" class="dropdown-item"><i class="icon-comment-discussion"></i> Messages <span class="badge badge-pill bg-blue ml-auto">58</span></a>
						<div class="dropdown-divider"></div>
						<a href="#" class="dropdown-item"><i class="icon-cog5"></i> Account settings</a>
						<a href="#" class="dropdown-item"><i class="icon-switch2"></i> Logout</a>
					</div>
				</li>
			</ul>
		</div>
	</div>
	<!-- /main navbar -->


	<!-- Secondary navbar -->
	<div class="navbar navbar-expand-md navbar-light">
		<div class="text-center d-md-none w-100">
			<button type="button" class="navbar-toggler dropdown-toggle" data-toggle="collapse" data-target="#navbar-navigation">
				<i class="icon-unfold mr-2"></i>
				Navigation
			</button>
		</div>

		<div class="navbar-collapse collapse" id="navbar-navigation">
			<ul class="navbar-nav">
				<li class="nav-item">
					<a href="index.html" class="navbar-nav-link active">
						<i class="icon-home4 mr-2"></i>
						Dashboard
					</a>
				</li>
				<li class="nav-item nav-item-levels mega-menu-full">
					<a href="#" class="navbar-nav-link dropdown-toggle" data-toggle="dropdown">
						<i class="icon-make-group mr-2"></i>
						Navigation
					</a>
					
				</li>
				<li class="nav-item dropdown">
					<a href="#" class="navbar-nav-link dropdown-toggle" data-toggle="dropdown">
						<i class="icon-strategy mr-2"></i>
						Starter kit
					</a>
				</li>
			</ul>
		</div>
	</div>
	<!-- /secondary navbar -->


	<!-- Page header -->
	<div class="page-header">
		<div class="page-header-content header-elements-md-inline">
			<div class="header-elements d-none py-0 mb-3 mb-md-0">
				<div class="breadcrumb">
					<a href="#" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
					<span class="breadcrumb-item active"> {{ $title }}</span>
				</div>
			</div>
		</div>
	</div>
	<!-- /page header -->
		

	<!-- Page content -->
	<div class="page-content pt-0">

		<!-- Main content -->
		<div class="content-wrapper">

			<!-- Content area -->
			<div class="content">
				<!-- Dashboard content -->
				@yield('content')
				<!-- /dashboard content -->

			</div>
			<!-- /content area -->

		</div>
		<!-- /main content -->

	</div>
	<!-- /page content -->


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
				&copy; 2015 - 2018. <a href="#">Limitless Web App Kit</a> by <a href="http://themeforest.net/user/Kopyov" target="_blank">Eugene Kopyov</a>
			</span>

			<ul class="navbar-nav ml-lg-auto">
				<li class="nav-item"><a href="https://kopyov.ticksy.com/" class="navbar-nav-link" target="_blank"><i class="icon-lifebuoy mr-2"></i> Support</a></li>
				<li class="nav-item"><a href="http://demo.interface.club/limitless/docs/" class="navbar-nav-link" target="_blank"><i class="icon-file-text2 mr-2"></i> Docs</a></li>
				<li class="nav-item"><a href="https://themeforest.net/item/limitless-responsive-web-application-kit/13080328?ref=kopyov" class="navbar-nav-link font-weight-semibold"><span class="text-pink-400"><i class="icon-cart2 mr-2"></i> Purchase</span></a></li>
			</ul>
		</div>
	</div>
	<!-- /footer -->
	<script>
		var base = "{{ url('/') }}/";
	</script>
	@yield('additional-script')
</body>
</html>
