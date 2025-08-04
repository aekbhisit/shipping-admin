@extends('layouts.customapp')

@section('custom-styles')

@endsection

@section('content')

			<!-- page-content -->
			<div class="page-content">
				<div class="container text-center text-dark">
					<div class="row">
						<div class="col-lg-4 d-block mx-auto">
							<div class="row">
								<div class="col-xl-12 col-md-12 col-md-12">
									<div class="card">
										<div class="card-body">
											<div class="text-center mb-6">
												<img src="{{URL::asset('assets/images/admin/logo.png')}}" class="" alt="">
											</div>
											<h3>Forgot password</h3>

											@if($errors->any())
												<div class="alert alert-danger mb-0" role="alert">
													<span class="alert-inner--icon"><i class="fe fe-slash"></i></span>
													<span class="alert-inner--text"><strong>{{$errors->first()}}</span>
												</div>
											@endif

											@if (session('status'))
												<div class="alert alert-success" role="alert">
													<span class="alert-inner--icon"><i class="fe fe-thumbs-up"></i></span>
													<span class="alert-inner--text"><strong>Success!</strong> {{ session('status') }}</span>
												</div>
											    
											@endif
											
											<form action="/admin/reset-password" method="post">
												 @csrf
												<input type="hidden" name="token" value="{{ $token }}" class="form-control" >
												<div class="input-group  mr-auto ml-auto mb-4">
													<span class="input-group-addon bg-white"><i class="fa fa-envelope"></i></span>
													<input type="email" name="email" value="" class="form-control" placeholder="Email address">
												</div>
												<div class="input-group  mr-auto ml-auto mb-4">
													<span class="input-group-addon bg-white"><i class="fa fa-envelope"></i></span>
													<input type="password" name="password" value="" class="form-control" placeholder="Password">
												</div>
												<div class="input-group  mr-auto ml-auto mb-4">
													<span class="input-group-addon bg-white"><i class="fa fa-envelope"></i></span>
													<input type="password" name="password_confirmation" value="" class="form-control" placeholder="Password Confirmation">
												</div>
												<div class="text-center">
													<button type="submit" class="btn btn-primary btn-block">Submit</button>
												</div>
											</form>
											
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- page-content end -->

@endsection('content')

@section('custom-scripts')

@endsection