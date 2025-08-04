@extends('login.master')

@section('styles')
@endsection

@section('content')
    <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-3 row-cols-xxl-4">
        <div class="col mx-auto">
            <div class="card shadow-none">
                <div class="card-body">
                    <div class="border p-4 rounded">
                        <div class="text-center mb-4">
                            <h3 class="">Sign in</h3>
                            <p class="mb-0">Login to your account</p>
                        </div>
                        <div class="form-body">
                            <form class="row g-4" id="login_form" name="login_form" onsubmit="CheckLogin(); return false;"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="col-12">
                                    <label class="form-label">Username</label>
                                    <input type="text" name="username" id="username" class="form-control"
                                        placeholder="Username">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Password</label>
                                    <div class="input-group" id="show_hide_password">
                                        <input type="password" class="form-control border-end-0"name="password"
                                            placeholder="Enter Password">
                                        <a href="#" class="input-group-text bg-transparent"><i
                                                class='bx bx-hide'></i></a>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked>
                                        <label class="form-check-label" for="flexSwitchCheckChecked">Remember Me</label>
                                    </div>
                                </div>
                                {{-- <div class="col-md-6 text-end"> <a href="authentication-forgot-password.html">Forgot
                                        Password ?</a>
                                </div> --}}
                                <div class="col-12">
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bx bxs-lock-open"></i>
                                            Sign in</button>
                                    </div>
                                </div>
                                {{-- <div class="col-12 text-center">
                                    <p class="mb-0">Don't have an account yet? <a href="authentication-signup.html">Sign
                                            up here</a>
                                    </p>
                                </div> --}}
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->

    <!-- page-content -->
    {{-- <div class="page-content">
        <div class="container text-center text-dark">
            <div class="row">
                <div class="col-lg-4 d-block mx-auto">
                    <div class="row">
                        <div class="col-xl-12 col-md-12 col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-center mb-6">
                                        <img src="/storage/logo/logo.svg" class="" alt="">
                                    </div>
                                    @if ($errors->any())
                                        <div class="alert alert-danger mb-0" role="alert">
                                            <span class="alert-inner--icon"><i class="fe fe-slash"></i></span>
                                            <span class="alert-inner--text"><strong>{{ $errors->first() }}</span>
                                        </div>
                                    @endif

                                    @if (session('status'))
                                        <div class="alert alert-success" role="alert">
                                            <span class="alert-inner--icon"><i class="fe fe-thumbs-up"></i></span>
                                            <span class="alert-inner--text"><strong>Success!</strong>
                                                {{ session('status') }}</span>
                                        </div>
                                    @endif

                                    <form id="login_form" name="login_form" onsubmit="CheckLogin(); return false;"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <h3>Login</h3>
                                        <p class="text-muted">Sign In to your account</p>
                                        <div class="input-group mb-3">
                                            <span class="input-group-addon bg-white"><i class="fa fa-user"></i></span>
                                            <input type="text" name="username" id="username" class="form-control"
                                                placeholder="Username">
                                        </div>
                                        <div class="input-group mb-4">
                                            <span class="input-group-addon bg-white"><i class="fa fa-unlock-alt"></i></span>
                                            <input type="password" name="password" id="password" class="form-control"
                                                placeholder="Password">
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary btn-block">Login</button>
                                            </div>
                                            <!-- <div class="col-12">
                                                             <a href="{{ url('forgot-password') }}" class="btn btn-link box-shadow-0 px-0">Forgot password?</a>
                                                            </div> -->
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div> --}}
    <!-- page-content end -->
@endsection

@section('scripts')

    <!-- Notifications js -->
    <link href="{{ URL::asset('assets/plugins/notify-growl/css/jquery.growl.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/notify-growl/css/notifIt.css') }}" rel="stylesheet" />
    <script src="{{ URL::asset('assets/plugins/bootbox/bootbox.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/notify-growl/js/rainbow.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/notify-growl/js/jquery.growl.js') }}"></script>
    <!-- admin js css -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <script src="{{ asset('js/admin.js') }}"></script>

    <!-- module js css -->
    <link rel="stylesheet" href="{{ mix('css/user.css') }}">
    <script src="{{ mix('js/user.js') }}?d=<?= time() ?>"></script>
@endsection
